<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">

        * { margin: 0; padding: 0; }
        body { font: 10px/1.4 Georgia, serif; }
        #page-wrap { width: 800px; margin: 0 auto; }

        .large-font { font-size: 18px; }
        .medium-font { font-size: 12px; }
        textarea { border: 0; font: 14px Georgia, Serif; text-align: center; }
        table { border-collapse: collapse; }
        table th { border: 1px solid black;  }

        .text-center {
            text-align: center;
        }

        .header-left {
            width: 400px; height: 80px; float: left;
        }

        #items { clear: both; width: 100%; margin: 30px 0 0 0; }
        #items textarea { width: 80px; height: 50px; }
        #items tr.item-row td { border: 0; vertical-align: top; text-align: center; }
        #items tr.summary-row td {  vertical-align: top; text-align: right; }
    </style>

    <title>Billing - {{ $billing->file_name }}</title>

</head>

<body data-gr-c-s-loaded="true">

<div id="page-wrap">

    <div class="medium-font" style="text-align: center;margin-bottom: 30px;">
        <b class="large-font">PPS GLOBAL NETWORK SDN BHD</b><br>
        [201401044898 (1121080-K)]<br>
        Formerly known as Printpackship Sdn Bhd<br>
        No 12, Jalan SS 21/35, Damansara Utama,<br>
        47400 Petaling Jaya, Selangor.<br>
    </div>
    <div class="header-left">
        Attn:<br>
        <strong>{{ $billing->branch->owner }}</strong><br>
        {{ $billing->branch->registered_company_name }}<br>
        {{ $billing->branch->address }}<br>
        HP: {{ $billing->branch->contact }}
        <br>
    </div>

    <div>
        <table cellpadding="3" width="100%">
            <tbody>
            <tr>
                <td colspan="3"><b class="medium-font">Invoice</b></td>
            </tr>
            <tr>
                <td>Account No</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Invoice No</td>
                <td>:</td>
                <td>{{ $billing->invoice_no }}</td>
            </tr>
            <tr>
                <td>Invoice Date</td>
                <td>:</td>
                <td>{{ $billing->billing_end->toDateString() }}</td>
            </tr>
            <tr style="border: 1px solid black">
                <td colspan="3" style="text-align: center">
                    Billing Period<br>
                    <b>{{ $billing->billing_start->toDateString() }} - {{ $billing->billing_end->toDateString() }}</b>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="clear:both"></div>

    <div style="height: 200px">
       @include("billing.table", ["billing" => $billing, "summary" => true])
    </div>

    <div>
        All cheque must be crossed & made payable to : <br>
        PPS GLOBAL NETWORK SDN BHD<br>
        Maybank Account No. 5647 1751 4505<br><br>
        To ensure your payment is duly recorded, kindly forward us details of your remittance by email to<br>
        ppsinvoice@gmail.com
    </div>
    <br>
    <div class="text-center">
        **This is computer generated. No signature required.**
    </div>

</div>
</body></html>