<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">

        * { margin: 0; padding: 0; }
        body { font: 10px/1.4 Georgia, serif; }
        #page-wrap { width: 800px; margin: 0 auto; }

        textarea { border: 0; font: 14px Georgia, Serif; text-align: center; }
        table { border-collapse: collapse; }
        table td, table th { border: 1px solid black;  }

        .border-bottom {
            border-bottom: 2px solid #000;
        }

        .text-center {
            text-align: center; font-size: 14px;
        }

        .text-left {
            text-align: left; font-size: 14px;
        }

        .text-right {
            text-align: right;
        }

        .header-left {
            width: 400px; height: 80px; float: left;
        }

        .header-center {
            text-align: center; vertical-align: middle; line-height: 100px;height: 100px; font-size: 30px; font-weight: bold; float: left;
        }

        .font-header{
            font-size: 20px; font-weight: bold;
        }

        .meta {
            margin-top: 1px; width: 300px; float: right;
        }

        .meta-head {
            text-align: left; background: #eee; width: 150px; font-size: 16px; font-weight: bold;
        }

        .meta-detail{
            text-align: left; background: #eee; width: 140px; font-size: 14px;
        }

        .header-note {
            width: 250px; height: 80px; float: left; font-size: 30px; font-weight: bold; text-align: center; vertical-align: middle;
        }

        .instructions {
            width: 750px ; height: 120px; float: left; border: solid black 1px; border-radius: 15px; padding: 20px;
        }

        .border-top {
            border-top: 2px solid #000;
        }

        .outer-border {
            border: 1px solid #ccc; border-radius: 15px; padding: 20px;
        }

        .bottom-left{
            width: 400px; height: 1px; float: left;
        }

        .bottom-center{
            width: 1000px; height: 10px; float: right;
        }

        .bottom-left-1{
            width: 300px; height: 50px; float: left;
        }
        .bottom-right{
            width: 300px; float: right;
        }

        #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
        #items th { background: #eee; }
        #items textarea { width: 80px; height: 50px; }
        #items tr.item-row td { border: 0; vertical-align: top; }

    </style>

    <title>Dropoff - {{ $dropoff->dropoff_no }}</title>

</head>

<body data-gr-c-s-loaded="true">

<div id="page-wrap">

    <div class="header-left">
        <strong class="font-header" style="font-size: 25px;">{{ $dropoff->branch->owner }}</strong><br>
        {{ $dropoff->branch->registered_company_name }} (<small><i>{{ $dropoff->branch->registration_no }}</i></small>)<br>
        <br>
        <br>
        {{ $dropoff->branch->address }}<br>
        Phone: {{ $dropoff->branch->contact }}
        <br>
    </div>

    @if(isset($admin))
        <div style="text-align: right;">
            {!!  str_replace('<?xml version="1.0" encoding="UTF-8"?>', "", QrCode::format('svg')->size(100)->generate($dropoff->pickup_url)) !!}
        </div>
    @endif

    <div class="header-left header-center ">Dropoff Slip</div>

    <div>
        <table cellpadding="3">
            <tbody>
            <tr>
                <td class="meta-head">Dropoff #</td>
                <td><textarea>{{$dropoff->dropoff_no}}</textarea></td>
            </tr>
            <tr>
                <td class="meta-head">Dropoff date</td>
                <td><textarea>{{$dropoff->created_at}}</textarea></td>
            </tr>
            <tr>
                <td class="meta-head">Dropoff service</td>
                <td><textarea>{{$dropoff->vendor->name}}</textarea></td>
            </tr>
            </tbody>
        </table>
    </div>

    @if($dropoff->customer)

        <br>

        <div class="outer-border">
            <strong class="font-header">{{ $dropoff->customer->name}}</strong>
            <br>
            <div class="text-left">{{ $dropoff->customer->contact}}</div>
            <div class="text-left">{{$dropoff->customer->address1}}</div>
            <div class="text-left">{{$dropoff->customer->address2}}</div>
            <div class="text-left">{{$dropoff->customer->address3}}</div>
            <div class="text-left">{{$dropoff->customer->address4}}</div>

        </div>

    @endif

    <div style="clear:both"></div>

    <div style="height: 200px">
        <table id="items" cellpadding="5" >
            <tbody>
            <tr>
                <th>Parcel consignment notes</th>
            </tr>


            @foreach($dropoff->items as $dkey => $item)
                    <tr class="item-row">
                        <td class="text-center">{{ $item->consignment_note }}</td>
                    </tr>
            @endforeach

            @for($x=0; $x < max( 25 - count($dropoff->items), 0 ); $x++  )
                <tr class="item-row"><td style="height: 20px"></td></tr>
            @endfor


            </tbody>
        </table>
    </div>

    <br><br><br>

    <div class="header-left">
        <table>
            <tbody>
            <tr class="item-row">
                <td class="meta-head text-center" colspan='2'>Dropoff &amp; Attendant details</td>
            </tr>
            <tr class="item-row">
                <td class="meta-detail">Dropoff #</td>
                <td><textarea>{{$dropoff->dropoff_no}}</textarea></td>
            </tr>
            <tr class="item-row">
                <td class="meta-detail">Total items</td>
                <td><textarea>{{ $dropoff->items->count() }}</textarea></td>
            </tr>
            <tr class="item-row">
                <td class="meta-detail">Attendant</td>
                <td><textarea>{{$dropoff->user->name}}</textarea></td>
            </tr>
            </tbody>
        </table>
    </div>

    @if(isset($admin))
        <div>
            <table>
                <tbody>
                    <tr>
                        <td colspan="2" class="text-center"><b>For office use only</b></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 10px;">
                            <b>Picked up by: </b><br>
                            <br><br><br><br><br><br><br>

                        </td>

                    </tr>
                    <tr class="item-row">
                        <td class="meta-detail">Date/Time:</td>
                        <td><textarea></textarea></td>
                    </tr>
                    <tr class="item-row">
                        <td class="meta-detail">NRIC No:</td>
                        <td><textarea></textarea></td>
                    </tr>
                    <tr class="item-row">
                        <td class="meta-detail">Vehicle No:</td>
                        <td><textarea></textarea></td>
                    </tr>
                </tbody>
            </table>

        </div>
    @endif

</div>
</body></html>