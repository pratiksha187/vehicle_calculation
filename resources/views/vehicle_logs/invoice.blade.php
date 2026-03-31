@extends('layouts.app')

@section('content')
@php
function numberToWordsIndian($number)
{
    $number = round($number, 2);
    $no = floor($number);
    $point = round(($number - $no) * 100);

    $words = [
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    ];

    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

    $str = [];

    // Crore
    if ($no >= 10000000) {
        $crore = floor($no / 10000000);
        $str[] = convertTwoDigit($crore, $words) . ' Crore';
        $no = $no % 10000000;
    }

    // Lakh
    if ($no >= 100000) {
        $lakh = floor($no / 100000);
        $str[] = convertTwoDigit($lakh, $words) . ' Lakh';
        $no = $no % 100000;
    }

    // Thousand
    if ($no >= 1000) {
        $thousand = floor($no / 1000);
        $str[] = convertTwoDigit($thousand, $words) . ' Thousand';
        $no = $no % 1000;
    }

    // Hundred
    if ($no >= 100) {
        $hundred = floor($no / 100);
        $str[] = $words[$hundred] . ' Hundred';
        $no = $no % 100;
    }

    // Last two digits
    if ($no > 0) {
        if (!empty($str)) {
            $str[] = 'and ' . convertTwoDigit($no, $words);
        } else {
            $str[] = convertTwoDigit($no, $words);
        }
    }

    $result = implode(' ', $str);

    if ($point > 0) {
        $result .= ' and ' . convertTwoDigit($point, $words) . ' Paise';
    }

    return trim($result) . ' Rupees Only';
}

function convertTwoDigit($number, $words)
{
    if ($number < 21) {
        return $words[$number];
    }

    $tens = floor($number / 10) * 10;
    $unit = $number % 10;

    return trim($words[$tens] . ' ' . $words[$unit]);
}
$invoiceNo = 'PE-' . $vehicle_log->from_date->format('Y-m') . '-' . str_pad($vehicle_log->id, 4, '0', STR_PAD_LEFT);
<!-- $invoiceNo = 'VPS-' . str_pad($vehicle_log->id, 4, '0', STR_PAD_LEFT); -->
$invoiceDate = now()->format('d-m-Y');
$amountInWords = numberToWordsIndian($netPayable);
@endphp

<style>
    .invoice-wrapper{
        background:#fff;
        padding:30px;
        border:1px solid #000;
        max-width:1000px;
        margin:auto;
    }
    .invoice-title{
        text-align:center;
        font-size:28px;
        font-weight:700;
        margin-bottom:20px;
        text-transform:uppercase;
    }
    .invoice-meta{
        display:flex;
        justify-content:space-between;
        font-size:14px;
        margin-bottom:15px;
    }
    .party-table,
    .item-table,
    .summary-table{
        width:100%;
        border-collapse:collapse;
        margin-bottom:15px;
    }
    .party-table td,
    .item-table th,
    .item-table td,
    .summary-table td{
        border:1px solid #000;
        padding:8px;
        vertical-align:top;
        font-size:14px;
    }
    .item-table th{
        background:#f2f2f2;
        text-align:center;
    }
    .bold{ font-weight:700; }
    .right{ text-align:right; }
    .center{ text-align:center; }
    .signature-section{
        margin-top:50px;
        text-align:right;
    }
    .company-name{
        font-weight:700;
        font-size:18px;
    }
    .small-text{
        font-size:13px;
        line-height:1.6;
    }
    @media print {
        .no-print{ display:none !important; }
        body{ background:#fff !important; }
        .card{ border:none !important; box-shadow:none !important; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h3>Tax Invoice</h3>
    <div>
        <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
        <button onclick="window.print()" class="btn btn-dark">Print Invoice</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="invoice-wrapper">
            <div class="invoice-title">Tax Invoice</div>

            <div class="invoice-meta">
                <div><span class="bold">Invoice No:</span> {{ $invoiceNo }}</div>
                <div><span class="bold">Date:</span> {{ $invoiceDate }}</div>
            </div>

            <table class="party-table">
                <tr>
                    <td width="50%">
                        <div class="bold mb-2">Invoice To :</div>
                        <div class="company-name">Shreeyash Construction</div>
                        <div class="small-text">
                            Crescent Pearl - B B-G/1, Veena Nagar,<br>
                            Katrang Road, Khopoli, Maharashtra 410203<br>
                            Contact: +91 9823849301<br>
                            Email: shreeyash.const@gmail.com<br>
                            GSTIN: 27AKPPP2912F2Z0
                        </div>
                    </td>
                    <td width="50%">
                        <div class="bold mb-2">Seller :</div>
                        <div class="company-name">{{ $vehicle_log->vehicle->vehicle_name }}</div>
                        <div class="small-text">
                            Address: Nimbode,khalapur<br>
                            Contact: +91 9309886247<br>
                            Email: priyanshenterprise28@gmail.com<br>
                            GSTIN: 
                        </div>
                    </td>
                </tr>
            </table>

            <div class="bold mb-2">Items</div>

            <table class="item-table">
                <thead>
                    <tr>
                        <th width="6%">Sr</th>
                        <th>Description</th>
                        <th width="16%">Amount</th>
                        <th width="14%">TDS (%)</th>
                        <th width="16%">TDS Amount</th>
                        <th width="16%">Net Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>
                            Vehicle monthly billing charges for
                            <strong>{{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}</strong>
                            for the period
                            <strong>{{ $vehicle_log->from_date->format('d-m-Y') }} to {{ $vehicle_log->to_date->format('d-m-Y') }}</strong>.
                            <br><br>
                            Fixed Amount: ₹ {{ number_format($vehicle_log->fixed_monthly_amount, 2) }}<br>
                            OT Hours: {{ $vehicle_log->formatted_ot }}<br>
                            OT Amount: ₹ {{ number_format($vehicle_log->total_ot_amount, 2) }}<br>
                            Total KM: {{ $vehicle_log->total_km }}<br>
                            Diesel Total: {{ number_format($vehicle_log->diesel_total, 2) }}
                        </td>
                        <td class="right">₹ {{ number_format($subtotal, 2) }}</td>
                        <td class="center">{{ $tdsPercent }}%</td>
                        <td class="right">₹ {{ number_format($tdsAmount, 2) }}</td>
                        <td class="right">₹ {{ number_format($netPayable, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="summary-table">
                <tr>
                    <td class="right" width="84%"><strong>Subtotal:</strong></td>
                    <td class="right" width="16%">₹ {{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>TDS ({{ $tdsPercent }}%):</strong></td>
                    <td class="right">₹ {{ number_format($tdsAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Total:</strong></td>
                    <td class="right"><strong>₹ {{ number_format($netPayable, 2) }}</strong></td>
                </tr>
            </table>

            <div class="mt-4 small-text">
                <strong>Total (In Words):</strong> {{ $amountInWords }}
            </div>

            <div class="signature-section">
               
                <div class="bold">Pratiksha Nikesh Misal</div>
                 <div class="small-text mb-5">Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>
@endsection