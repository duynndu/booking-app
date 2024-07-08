<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Booking;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('booking')->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|integer',
            'payment_method' => 'required|string',
        ]);

        $invoice = Invoice::create($request->all());
        return response()->json($invoice, 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with('booking')->find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|integer',
            'payment_method' => 'required|string',
        ]);

        $invoice->update($request->all());
        return response()->json($invoice);
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
