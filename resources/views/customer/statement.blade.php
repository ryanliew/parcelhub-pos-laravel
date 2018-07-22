<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
		* { margin: 0; padding: 0; }
		body { font: 10px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 14px Georgia, Serif; }
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
	    	text-align: center; vertical-align: middle; line-height: 22px; height: 50px; font-size: 30px; font-weight: bold; float: left;
	    }

	    .font-header{
	    	font-size: 20px; font-weight: bold; 
	    }

	    .meta {
	    	 margin-top: 1px; width: 300px; float: right; 
	    }

	    .meta-head {
	    	text-align: left; background: #eee; width: 150px; font-size: 16px;
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
	
	<title>Statement Of Account</title>
	
</head>

<body data-gr-c-s-loaded="true">

	<div id="page-wrap">

		<div class="header-left">
			<strong class="font-header">{{ $customer->branch->name }}</strong><br>
			Co Reg No: {{ $customer->branch->registration_no }}
			<br>
			<br>
			{{ $customer->branch->address }}<br>
			Phone: {{ $customer->branch->contact }}
			<br>
			<br>
			<br>
		</div>
		<div><img id="image" src="img/logo.png" alt="logo"></div>
	
		<div class="outer-border header-left header-center">
			<div class="text-left">{{ $customer->name}}</div>
			<br>
			<div class="text-left">{{$customer->address1}}</div>
			<div class="text-left">{{$customer->address2}}</div>
			<div class="text-left">{{$customer->address3}}</div>
			<div class="text-left">{{$customer->address4}}</div>

		</div>

		<div>
			<table cellpadding="3">
                <tbody>
                <tr>
                    <td class="meta-head" colspan="2" style="text-align: center; font-size: 25px">Statement of account</td>
                    <!-- <td><textarea>Account sale</textarea></td> -->
                </tr>
                <tr>
                    <td class="meta-head">Total Debit</td>
                    <td><textarea></textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Total credit</td>
                    <td><textarea></textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Closing Balance</td>
                    <td><textarea></textarea></td>
                </tr>
                </tbody>
            </table>
		</div>
		
		<br>

		


	</div>

</body></html>