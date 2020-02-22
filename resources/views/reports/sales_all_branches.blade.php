@extends('layouts.admin')

@section('page')
	Sales report
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/rg-1.1.0/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card mt-5">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">	
						<h1>Sales report</h1>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<div class="d-flex align-items-center">
					<b class="flex-grow-1">Period: {{ request()->from }} - {{ request()->to }}</b>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="branches-table">
					<thead>
						<tr>
							<th>Branch</th>
							<th>Total Sales Amount</th>
						</tr>
					</thead>
					<tbody>
                        @foreach($report_detail as $detail)
							<tr>                               
                                <td><a onclick="generate(this)">{{ $detail['branch']->owner }}</a></td>
                                <td>RM{{ number_format($detail['items']->sum('total_price_after_discount'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>		
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.18/rg-1.1.0/datatables.min.js"></script>
	<script>
		$(function(){

			var table = $("#branches-table").DataTable({
				paging: false,
				searching: false
			});
        });	

        function generate(branch) {
          
            var report_detail = {!! json_encode($report_detail) !!};           
            let selected_detail = report_detail.find(element => element.branch.owner == branch.innerHTML );
            
            if( selected_detail ){
                window.open(this.location.href + "&branch=" + selected_detail.branch.id);
            }    
        }
	</script>
@endsection