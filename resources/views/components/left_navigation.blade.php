@if(auth()->check())
    <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
            <a id="invoice-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                Invoices <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="invoice-dropdown">
                <a class="dropdown-item" href="{{ route('invoices.page') }}">
                    View invoices
                </a>
                <a class="dropdown-item" href="{{ route('invoices.canceled') }}">
                    View canceled invoices
                </a>
                <a class="dropdown-item" href="{{ route('invoices.create') }}">
                    Create invoice (F9)
                </a>
            </div>
        </li>
        <!-- <li class="nav-item"><a class="nav-link" href="{{ route('customers.page') }}">Customers</a></li> -->
        <li class="nav-item dropdown">
            <a id="customer-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                Customers <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="customer-dropdown">
                <a class="dropdown-item" href="{{ route('customers.page') }}">
                    Customers
                </a>
                <a class="dropdown-item" href="{{ route('customers.type-statement') }}">
                    Customer statements
                </a>
            </div>
        </li>
         <!-- <li class="nav-item"><a class="nav-link" href="{{ route('payments.page') }}">Payment overview</a></li> -->
         <li class="nav-item dropdown">
            <a id="invoice-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                Payments <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="invoice-dropdown">
                <a class="dropdown-item" href="{{ route('payments.page') }}">
                    Payment overview
                </a>
                <a class="dropdown-item" href="{{ route('payments.receive') }}">
                    Payment receive
                </a>
            </div>
        </li>

        <li class="nav-item"><a class="nav-link" href="{{ route('cashups.page') }}">Cash up reports</a></li>
        
        <li class="nav-item"><a class="nav-link" href="{{ route('groups.page') }}">Customer groups</a></li>
        
        <li class="nav-item"><a class="nav-link" href="{{ route('parcels.page') }}">Check in parcels</a></li>
        <!-- <li class="nav-item"><a class="nav-link" href="{{ route('profit-and-loss.page') }}">Profit & Loss</a></li> -->

    </ul>
@endif