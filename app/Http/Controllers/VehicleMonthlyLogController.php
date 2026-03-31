<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDailyEntry;
use App\Models\VehicleMonthlyLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleMonthlyLogController extends Controller
{
    public function index()
    {
        $logs = VehicleMonthlyLog::with('vehicle')->latest()->paginate(10);
        return view('vehicle_logs.index', compact('logs'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('vehicle_name')->get();
        return view('vehicle_logs.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $alreadyExists = VehicleMonthlyLog::where('vehicle_id', $request->vehicle_id)
            ->where('from_date', $request->from_date)
            ->where('to_date', $request->to_date)
            ->exists();

        if ($alreadyExists) {
            return back()->with('error', 'This monthly sheet already exists.');
        }

        DB::transaction(function () use ($request) {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            $vehicle_log = VehicleMonthlyLog::create([
                'vehicle_id' => $vehicle->id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'fixed_monthly_amount' => $vehicle->fixed_monthly_amount,
                'tds_percent' => $vehicle->tds_percent,
                'opening_reading' => 0,
                'closing_reading' => 0,
                'total_km' => 0,
                'diesel_total' => 0,
                'average_kmpl' => 0,
                'total_ot_minutes' => 0,
                'total_ot_hours' => 0,
                'total_ot_amount' => 0,
                'total_billing_amount' => $vehicle->fixed_monthly_amount,
                'tds_amount' => 0,
                'net_payable' => $vehicle->fixed_monthly_amount,
            ]);

            $period = CarbonPeriod::create($request->from_date, $request->to_date);

            foreach ($period as $date) {
                VehicleDailyEntry::create([
                    'vehicle_monthly_log_id' => $vehicle_log->id,
                    'entry_date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'challan_no' => null,
                    'diesel_added' => 0,
                    'start_reading' => 0,
                    'end_reading' => 0,
                    'total_km' => 0,
                    'in_time' => null,
                    'out_time' => null,
                    'total_minutes' => 0,
                    'ot_minutes' => 0,
                    'remark' => null,
                ]);
            }
        });

        return redirect()->route('vehicle-logs.index')->with('success', 'Monthly sheet created successfully.');
    }

    public function show(VehicleMonthlyLog $vehicle_log)
    {
        $vehicle_log->load(['vehicle', 'dailyEntries']);
        return view('vehicle_logs.show', compact('vehicle_log'));
    }
    public function bill(VehicleMonthlyLog $vehicle_log)
{
    $vehicle_log->load(['vehicle', 'dailyEntries']);
    return view('vehicle_logs.bill', compact('vehicle_log'));
}


public function invoice(VehicleMonthlyLog $vehicle_log)
{
    $vehicle_log->load(['vehicle', 'dailyEntries']);

    $subtotal = $vehicle_log->total_billing_amount;
    $tdsPercent = $vehicle_log->tds_percent;
    $tdsAmount = $vehicle_log->tds_amount;
    $netPayable = $vehicle_log->net_payable;

    return view('vehicle_logs.invoice', compact(
        'vehicle_log',
        'subtotal',
        'tdsPercent',
        'tdsAmount',
        'netPayable'
    ));
}

    public function edit(VehicleMonthlyLog $vehicle_log)
    {
        $vehicle_log->load(['vehicle', 'dailyEntries']);
        return view('vehicle_logs.edit', compact('vehicle_log'));
    }

    public function update(Request $request, VehicleMonthlyLog $vehicle_log)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.entry_date' => 'required|date',
            'entries.*.challan_no' => 'nullable|string|max:255',
            'entries.*.diesel_added' => 'nullable|numeric|min:0',
            'entries.*.start_reading' => 'nullable|integer|min:0',
            'entries.*.end_reading' => 'nullable|integer|min:0',
            'entries.*.in_time' => 'nullable',
            'entries.*.out_time' => 'nullable',
            'entries.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $vehicle_log) {
            $vehicle_log->dailyEntries()->delete();

            foreach ($request->entries as $row) {
                $startReading = !empty($row['start_reading']) ? (int)$row['start_reading'] : 0;
                $endReading   = !empty($row['end_reading']) ? (int)$row['end_reading'] : 0;
                $dieselAdded  = !empty($row['diesel_added']) ? (float)$row['diesel_added'] : 0;

                $totalKm = ($endReading >= $startReading && $startReading > 0)
                    ? ($endReading - $startReading)
                    : 0;

                $minutesData = $this->calculateMinutes($row['in_time'] ?? null, $row['out_time'] ?? null);

                VehicleDailyEntry::create([
                    'vehicle_monthly_log_id' => $vehicle_log->id,
                    'entry_date' => $row['entry_date'],
                    'day' => Carbon::parse($row['entry_date'])->format('D'),
                    'challan_no' => $row['challan_no'] ?? null,
                    'diesel_added' => $dieselAdded,
                    'start_reading' => $startReading,
                    'end_reading' => $endReading,
                    'total_km' => $totalKm,
                    'in_time' => !empty($row['in_time']) ? $row['in_time'] : null,
                    'out_time' => !empty($row['out_time']) ? $row['out_time'] : null,
                    'total_minutes' => $minutesData['total_minutes'],
                    'ot_minutes' => $minutesData['ot_minutes'],
                    'remark' => $row['remark'] ?? null,
                ]);
            }

            $this->recalculateMonthlyLog($vehicle_log);
        });

        return redirect()->route('vehicle-logs.show', $vehicle_log->id)->with('success', 'Monthly log updated successfully.');
    }

    public function destroy(VehicleMonthlyLog $vehicle_log)
    {
        $vehicle_log->delete();
        return redirect()->route('vehicle-logs.index')->with('success', 'Monthly log deleted successfully.');
    }

    public function dailyEntry(VehicleMonthlyLog $vehicle_log)
    {
        $vehicle_log->load(['vehicle', 'dailyEntries']);
        return view('vehicle_logs.daily_entry', compact('vehicle_log'));
    }

    public function saveDailyEntry(Request $request, VehicleMonthlyLog $vehicle_log)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.id' => 'required|exists:vehicle_daily_entries,id',
            'entries.*.challan_no' => 'nullable|string|max:255',
            'entries.*.diesel_added' => 'nullable|numeric|min:0',
            'entries.*.start_reading' => 'nullable|integer|min:0',
            'entries.*.end_reading' => 'nullable|integer|min:0',
            'entries.*.in_time' => 'nullable',
            'entries.*.out_time' => 'nullable',
            'entries.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $vehicle_log) {
            foreach ($request->entries as $row) {
                $entry = VehicleDailyEntry::findOrFail($row['id']);

                $startReading = !empty($row['start_reading']) ? (int)$row['start_reading'] : 0;
                $endReading   = !empty($row['end_reading']) ? (int)$row['end_reading'] : 0;
                $dieselAdded  = !empty($row['diesel_added']) ? (float)$row['diesel_added'] : 0;

                $totalKm = ($endReading >= $startReading && $startReading > 0)
                    ? ($endReading - $startReading)
                    : 0;

                $minutesData = $this->calculateMinutes($row['in_time'] ?? null, $row['out_time'] ?? null);

                $entry->update([
                    'challan_no' => $row['challan_no'] ?? null,
                    'diesel_added' => $dieselAdded,
                    'start_reading' => $startReading,
                    'end_reading' => $endReading,
                    'total_km' => $totalKm,
                    'in_time' => !empty($row['in_time']) ? $row['in_time'] : null,
                    'out_time' => !empty($row['out_time']) ? $row['out_time'] : null,
                    'total_minutes' => $minutesData['total_minutes'],
                    'ot_minutes' => $minutesData['ot_minutes'],
                    'remark' => $row['remark'] ?? null,
                ]);
            }

            $this->recalculateMonthlyLog($vehicle_log);
        });

        return redirect()->route('vehicle-logs.daily-entry', $vehicle_log->id)->with('success', 'Daily entries saved successfully.');
    }

    private function recalculateMonthlyLog(VehicleMonthlyLog $vehicle_log): void
    {
        $vehicle_log->load(['dailyEntries', 'vehicle']);

        $entries = $vehicle_log->dailyEntries->sortBy('entry_date');

        $opening = 0;
        $closing = 0;
        $dieselTotal = 0;
        $totalOtMinutes = 0;
        $firstReadingFound = false;

        foreach ($entries as $entry) {
            if (!$firstReadingFound && $entry->start_reading > 0) {
                $opening = $entry->start_reading;
                $firstReadingFound = true;
            }

            if ($entry->end_reading > 0) {
                $closing = $entry->end_reading;
            }

            $dieselTotal += (float)$entry->diesel_added;
            $totalOtMinutes += (int)$entry->ot_minutes;
        }

        $readingDiff = ($closing >= $opening) ? ($closing - $opening) : 0;
        $average = $dieselTotal > 0 ? ($readingDiff / $dieselTotal) : 0;
        $totalOtHours = $totalOtMinutes / 60;
        $totalOtAmount = $totalOtHours * (float)$vehicle_log->vehicle->ot_rate_per_hour;
        $totalBilling = (float)$vehicle_log->vehicle->fixed_monthly_amount + $totalOtAmount;
        $tdsAmount = ($totalBilling * (float)$vehicle_log->vehicle->tds_percent) / 100;
        $netPayable = $totalBilling - $tdsAmount;

        $vehicle_log->update([
            'opening_reading' => $opening,
            'closing_reading' => $closing,
            'total_km' => $readingDiff,
            'diesel_total' => round($dieselTotal, 2),
            'average_kmpl' => round($average, 4),
            'total_ot_minutes' => $totalOtMinutes,
            'total_ot_hours' => round($totalOtHours, 2),
            'total_ot_amount' => round($totalOtAmount, 2),
            'total_billing_amount' => round($totalBilling, 2),
            'tds_amount' => round($tdsAmount, 2),
            'net_payable' => round($netPayable, 2),
        ]);
    }

    private function calculateMinutes(?string $inTime, ?string $outTime): array
    {
        if (!$inTime || !$outTime) {
            return [
                'total_minutes' => 0,
                'ot_minutes' => 0,
            ];
        }

        $in = Carbon::createFromFormat('H:i', date('H:i', strtotime($inTime)));
        $out = Carbon::createFromFormat('H:i', date('H:i', strtotime($outTime)));

        if ($out->lessThan($in)) {
            $out->addDay();
        }

        $totalMinutes = $in->diffInMinutes($out);
        $otMinutes = max(0, $totalMinutes - 720);

        return [
            'total_minutes' => $totalMinutes,
            'ot_minutes' => $otMinutes,
        ];
    }

    
}