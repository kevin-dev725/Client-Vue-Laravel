<?php

namespace Tests\Unit;

use App\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetTotalEarnings()
    {
        /**
         * @var Collection $invoices
         */
        $invoices = factory(Invoice::class, 5)->states('complete')->create();
        $this->assertTrue(method_exists(Invoice::class, 'getTotalEarnings'), 'Method getTotalEarnings does not exist.');
        $this->assertEquals($invoices->sum('amount'), Invoice::getTotalEarnings());
    }

    public function testGetTotalEarningsBetweenDates()
    {
        $now = Carbon::now()->subDays(10);
        Carbon::setTestNow($now);
        $invoice_one = factory(Invoice::class)->states('complete')->create();
        Carbon::setTestNow($now->addDay());
        $invoice_two = factory(Invoice::class)->states('complete')->create();
        Carbon::setTestNow($now->addDay());
        factory(Invoice::class)->states('complete')->create();
        $this->assertTrue(method_exists(Invoice::class, 'getTotalEarningsBetween'), 'Method getTotalEarnings does not exist.');
        Carbon::setTestNow();
        $this->assertEquals($invoice_one->amount + $invoice_two->amount, Invoice::getTotalEarningsBetween(Carbon::now()->subDays(10), Carbon::now()->subDays(9)), 'Invoice::getTotalEarningsBetween does not return the correct amount.');
    }
}
