<?php

namespace App\Http\Controllers\Owner;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $invoicesQuery = Invoice::where('owner_id', $user->id)
            ->with(['tenant', 'bookingRequest.unit.property', 'items', 'payments']);

        $totalRevenue = (int) (clone $invoicesQuery)->where('status', InvoiceStatus::PAID)->sum('subtotal_amount');
        $totalDepositHeld = (int) (clone $invoicesQuery)->where('status', InvoiceStatus::PAID)->sum('deposit_amount');
        $totalPendingReceivables = (int) (clone $invoicesQuery)->where('status', InvoiceStatus::UNPAID)->sum('total_amount');

        $invoices = $invoicesQuery->latest()->paginate(10);

        return view('owner.finance.index', compact(
            'invoices',
            'totalRevenue',
            'totalDepositHeld',
            'totalPendingReceivables'
        ));
    }
}
