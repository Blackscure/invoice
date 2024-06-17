<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'invoice_number' => 'required|unique:invoices,invoice_number',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $invoice = Invoice::create($request->all());

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $invoices = Invoice::all();

            return response()->json([
                'message' => 'Invoices retrieved successfully',
                'invoices' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function show($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            return response()->json([
                'message' => 'Invoice retrieved successfully',
                'invoice' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invoice not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
