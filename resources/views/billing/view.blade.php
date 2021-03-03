@extends('layouts.admin')

@section('page')
    Billing Converter
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <b>Billing Converter</b>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <billing-import created_by="{{ auth()->id() }}"></billing-import>
                </div>
                <div class="mt-5">
                    <table class="table table-bordered" id="billings-table">
                        <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>File name</th>
                            <th>Import date</th>
                            <th>Invoice date</th>
                            <th>Billing start date</th>
                            <th>Billing end date</th>
                            <th>Payment term</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                    </table>
                    <div id="processingIndicator"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Loading...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
    <script>
        $(function(){
            var table = $("#billings-table").DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                colReorder: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                select: {
                    style: 'single'
                },
                dom: 'Blftip',
                buttons: [
                    {
                        text: 'Download bills',
                        // text: 'Edit',
                        action: function( e, dt, node, config ) {
                            var loc = "/admin/billings/download/" + table.rows({selected: true}).data().toArray()[0].id;

                            window.open(loc, '_blank');
                        },
                        enabled: false
                    },
                    {
                        text: 'Send',
                        // text: 'Edit',
                        action: function( e, dt, node, config ) {

                            var loc = "/admin/billings/send/" + table.rows({selected: true}).data().toArray()[0].id;

                            axios.get(loc)
                                .then(response => {
                                    flash(response.data.message);
                                })
                                .catch(error => {
                                    flash(error);
                                });
                        },
                        enabled: false
                    },
                    {
                        text: 'Delete',
                        // text: 'Edit',
                        action: function( e, dt, node, config ) {

                            var loc = "/admin/billings/delete/" + table.rows({selected: true}).data().toArray()[0].id;

                            axios.get(loc)
                                .then(response => {
                                    flash(response.data.message);
                                    window.events.$emit("reload-table");
                                })
                                .catch(error => {
                                    flash(error);
                                });

                            table.button( 0 ).enable( selectedRows === 1 );
                            table.button( 1 ).enable( selectedRows === 1 );
                            table.button( 2 ).enable( selectedRows === 1 );
                        },
                        enabled: false
                    },
                    {
                        extend: 'excel',
                        text: function ( dt, button, config ) {
                            return dt.i18n( 'buttons.excel', 'Generate excel' );
                        }
                    },
                    'colvis'
                ],
                ajax: '{!! route("billings.index") !!}',
                columns: [
                    {data: 'vendor_name'},
                    {data: 'file_name'},
                    {data: 'created_at'},
                    {data: 'invoice_date'},
                    {data: 'billing_start'},
                    {data: 'billing_end'},
                    {data: 'payment_term'},
                    {data: 'status'},
                    {data: 'progress'},
                    {data: 'total'},
                ],
                "order": [[ 0, "asc" ]],

                "rowCallback": function( row, data, index ) {
                    var allData = this.api().column(0).data().toArray();
                    var num = 0;
                    allData.forEach( function (trackingcode) {
                        if(trackingcode == data.tracking_code){
                            num ++;
                        }
                    })
                    if(num>1){
                        $('td', row).css('background-color', 'Yellow');
                    }
                }
            });

             table.on( 'select deselect', function () {
                 var selectedRows = table.rows( { selected: true } ).count();
                 table.button( 0 ).enable( selectedRows === 1 );
                 table.button( 1 ).enable( selectedRows === 1 );
                 table.button( 2 ).enable( selectedRows === 1 );
            });

            window.events.$on("reload-table", function(){
                table.ajax.reload();
            });

            table.on( 'processing.dt', function ( e, settings, processing ) {
                $('#processingIndicator').css( 'display', processing ? 'flex' : 'none' );
            });
        });


    </script>
@endsection