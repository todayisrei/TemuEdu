<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** Daftar pembayaran (lihat saja). */
    public function index(Request $request)
    {
        $payments = Payment::with(['user', 'consultant.user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('service'), fn ($q) => $q->where('service', $request->service))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $total = Payment::where('status', 'paid')->sum('amount');
        $refund = Payment::where('status', 'refund_pending')->count();

        return view('admin.payments.index', compact('payments', 'total', 'refund'));
    }
}
