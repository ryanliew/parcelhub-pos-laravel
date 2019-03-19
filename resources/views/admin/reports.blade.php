@extends('layouts.admin')

@section('page')
	Reports
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Reports</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="reports-table">
					<thead>
						<tr>
							<th>Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<td>Sales Report</td>
						<td><button class="btn btn-primary" onclick="window.events.$emit('generateSalesReport')">Generate</button></td>
					</tbody>
				</table>
			</div>
		</div>

		<sales-reports-dialog :user='{{ auth()->user() }}'></sales-reports-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#reports-table").DataTable();
		});

		
	</script>
@endsection