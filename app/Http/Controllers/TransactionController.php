<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Initialize query builder
        $query = Transaction::query();

        // Apply filters if they exist in the request
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->input('invoice_id'));
        }

        if ($request->has('phone_number')) {
            $query->where('phone_number', $request->input('phone_number'));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        // Fetch filtered transactions
        $transactions = $query->get();

        // Return transactions as JSON response
        return response()->json($transactions);
    }

}
