<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceTest extends TestCase
{
    /** @test */
    public function user_can_create_invoice()
    {
        $user = $this->signIn();

        $invoice = make('App\Invoice', ['branch_id' => $user->current_branch, 'terminal' => $user->current_terminal, 'created_by' => $user->id]); 

        $item = make("App\Item");

        $response = $this->json('POST', '/invoices', [ "invoice" => $invoice->toArray(),
                                                        "items" => [$item] 
                                                    ]);

        $this->assertDatabaseHas('invoices', ['total' => 100.00]);
        $this->assertDatabaseHas('items', ['invoice_id' => 1, 'total_price' => 100.00]);
    } 
}
