<?php

namespace App\Http\Controllers;

use App\Services\MPesaService;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class MPesaController extends Controller
{
    protected $mpesa;

    public function __construct(MPesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    public function pay(Request $request, $invoiceNumber)
    {
        try {
            // Get invoice based on invoice number from URL path
            $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

            if (!$invoice) {
                throw new ModelNotFoundException('Invoice not found');
            }

            // Initiate STK push payment
            $accessToken = $this->mpesa->getAccessToken(config('services.mpesa.consumer_key'), config('services.mpesa.consumer_secret'));

            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $response = $this->mpesa->initiatePayment(
                $accessToken,
                config('services.mpesa.shortcode'),
                config('services.mpesa.lipa_na_mpesa_online_passkey'),
                $invoice->amount,
                $request->phoneNumber,
                config('services.mpesa.callback_url'),
                $invoice->invoice_number,
                'Payment for Invoice ' . $invoice->invoice_number
            );

            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Invoice not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        // Handle the callback from M-Pesa here
        // Log the response or store it in the database
        \Log::info('M-Pesa Callback:', $request->all());

        return response()->json(['status' => 'success']);
    }



    // public function callback(Request $request)
    // {
    //     try {
    //         // Log the callback data
    //         \Log::info('MPesa Callback Data:', $request->all());

    //         // Example callback data validation
    //         $transactionData = $request->all();
    //         $request->validate([
    //             'Body.stkCallback' => 'required|array',
    //             'Body.stkCallback.CallbackMetadata' => 'required|array',
    //             'Body.stkCallback.CallbackMetadata.Item' => 'required|array'
    //         ]);

    //         $callback = $transactionData['Body']['stkCallback'];

    //         if ($callback['ResultCode'] == 0) {
    //             $items = $callback['CallbackMetadata']['Item'];
    //             $data = [];
    //             foreach ($items as $item) {
    //                 $data[$item['Name']] = $item['Value'];
    //             }

    //             // Example of extracted data (depends on M-Pesa response)
    //             $transactionId = $data['MpesaReceiptNumber'] ?? null;
    //             $phoneNumber = $data['PhoneNumber'] ?? null;
    //             $amount = $data['Amount'] ?? null;
    //             $invoiceNumber = $data['AccountReference'] ?? null;

    //             if (!$transactionId || !$phoneNumber || !$amount || !$invoiceNumber) {
    //                 throw new \Exception('Missing required callback data');
    //             }

    //             // Find the invoice
    //             $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();

    //             // Create a transaction record
    //             Transaction::create([
    //                 'invoice_id' => $invoice->id,
    //                 'transaction_id' => $transactionId,
    //                 'phone_number' => $phoneNumber,
    //                 'amount' => $amount,
    //                 'status' => 'success',
    //             ]);

    //             // Update invoice status
    //             $invoice->status = 'paid';
    //             $invoice->save();

    //             return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    //         } else {
    //             // Handle failed transaction
    //             return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Transaction Failed']);
    //         }
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'ResultCode' => 1,
    //             'ResultDesc' => 'Validation Error',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'ResultCode' => 1,
    //             'ResultDesc' => 'Invoice not found',
    //             'error' => $e->getMessage()
    //         ], 404);
    //     } catch (\Exception $e) {
    //         \Log::error('MPesa Callback Error:', ['exception' => $e]);
    //         return response()->json([
    //             'ResultCode' => 1,
    //             'ResultDesc' => 'An error occurred',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
