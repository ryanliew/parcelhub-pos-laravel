@extends('layouts.app')

@section('page')
    Dropoffs
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <b>Dropoffs</b>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="dropoff-table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Dropoff No.</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Vendor</th>
                        <th>Picked up on</th>
                        <th>Remarks</th>
                        <th>Vehicle no</th>
                        <th>Picked up by</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
    <script>
        $(function(){
            var table = $("#dropoff-table").DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                colReorder: true,
                order: [0, 'desc'],
                select: {
                    style: 'single'
                },
                dom: 'Blftip',
                buttons: [
                    {
                        text: 'Create (F4)',
                        // text: 'Edit',
                        action: function( e, dt, node, config ) {
                            var loc = "/dropoffs/create";

                            window.open(loc, '_blank');
                        },
                        enabled: true
                    },
                    {
                        text: 'View',
                        // text: 'Edit',
                        action: function( e, dt, node, config ) {
                            var loc = "/dropoffs/details/" + table.rows({selected: true}).data().toArray()[0].id;

                            window.open(loc, '_blank');
                        },
                        enabled: false
                    },
                    'excel', 'colvis',
                ],
                ajax: '{!! route("dropoff.index") !!}',
                columns: [
                    {data: 'created_at', render: function(data, type, row){
                            if(type === 'display' || type === 'filter') {
                                return moment(data).format("YYYY-MM-DD");
                            }

                            return data;
                        }, "searchable": false},
                    {data: 'dropoff_no'},
                    {data: 'customer', name: 'customer.name'},
                    {data: 'status'},
                    {data: 'vendor', name: 'vendor.name'},
                    {data: 'picked_up_on', render: function(data, type, row){
                            if(type === 'display' || type === 'filter') {
                                return data ? moment(data).format("YYYY-MM-DD") : "---";
                            }

                            return data;
                        }, "searchable": false},
                    {data: 'remarks', render: function(data, type, row){
                            if(type === 'display' || type === 'filter') {
                                return data ? data : "---";
                            }

                            return data;
                        }
                    },
                    {data: 'vehicle_no'},
                    {data: 'picked_up_by'}
                ]


            });

            table.on( 'select deselect', function () {
                var selectedRows = table.rows( { selected: true } ).count();

                table.button( 0 ).enable( selectedRows === 1 );
                table.button( 1 ).enable( selectedRows === 1 );
            });

            window.events.$on("reload-table", function(){
                table.ajax.reload();
            });
        });



    </script>
@endsection