@if(auth()->check())
    <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
            <a id="invoice-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                Sessions <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="invoice-dropdown">
                <a class="dropdown-item" href="nova/resources/invoices">
                    View invoices
                </a>
                <a class="dropdown-item" href="{{ route('invoices.create') }}">
                    Create session (F9)
                </a>
            </div>
        </li>
        {{-- <li class="nav-item"><a class="nav-link" href="{{ route('customers.page') }}">Customers</a></li> --}}

         <!-- <li class="nav-item"><a class="nav-link" href="{{ route('payments.page') }}">Payment overview</a></li> -->
         {{-- <li class="nav-item dropdown">
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
        </li> --}}
{{-- 
        <li class="nav-item"><a class="nav-link" href="{{ route('cashups.page') }}">Cash up reports</a></li> --}}

        <li class="nav-item"><a class="nav-link" href="/nova/resources/members">Members</a></li>
        
        {{-- <li class="nav-item"><a class="nav-link" href="{{ route('groups.page') }}">Customer groups</a></li> --}}

    </ul>
@endif