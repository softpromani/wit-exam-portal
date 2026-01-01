<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use App\Models\ExamSessionHasCBS;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function payProcess(int $examFormId)
    {
        try {
            DB::beginTransaction();

            $examForm = ExamForm::with('student')->findOrFail($examFormId);
            $student = $examForm->student;

            // Check if there's already a successful transaction
            $existingTransaction = $examForm->transactions()
                ->whereIn('status', ['success', 'completed'])
                ->first();

            if ($existingTransaction) {
                DB::commit();
                return redirect()->route('student.payment.success')
                    ->with('transaction', $existingTransaction)
                    ->with('message', 'Payment already completed for this exam form.');
            }

            // Fetch amount from ExamSessionHasCBS
            $cbsConfig = ExamSessionHasCBS::where('exam_session_id', $examForm->session_id)
                ->where('course_id', $student->course_id)
                ->where('branch_id', $student->branch_id)
                ->first();

            if (!$cbsConfig || !isset($cbsConfig->semester_amounts[$student->semester_id])) {
                throw new \Exception('Payment amount not configured for this semester/course.');
            }

            $amount = $cbsConfig->semester_amounts[$student->semester_id];

            $path = storage_path() . "/json/worldline_paymentgateway.json";
            if (!file_exists($path)) {
                throw new \Exception('Payment gateway configuration not found.');
            }

            $mer_array = json_decode(file_get_contents($path), true);

            if ($mer_array['typeOfPayment'] == "TEST") {
                $amount = 1.00;
            }

            // Standardize amount to 2 decimal places for hash consistency
            $amount = number_format($amount, 2, '.', '');

            $txnId = time() . '000' . rand(1111, 9999);
            $consumerId = 'ef' . $examForm->id;

            // Ensure mobile and email are consistent with showCheckout defaults if missing
            $mobile = $student->mobile_number ?? '8896287276';
            $email = $student->email ?? 'info@witlnmu.ac.in';

            // Build data string for hash
            $datastring = $mer_array['merchantCode'] . "|" . $txnId . "|" . $amount . "|" . "|" . $consumerId . "|" . $mobile . "|" . $email . "||||||||||" . $mer_array['salt'];

            $hashVal = hash('sha512', $datastring);

            Log::info('Payment Hash Generated', [
                'txnId' => $txnId,
                'data_string_masked' => str_replace($mer_array['salt'], '***SALT***', $datastring),
                'hash' => $hashVal
            ]);

            $paymentDetails = array(
                'merchantId' => $mer_array['merchantCode'],
                'txnId' => $txnId,
                'amount' => $amount,
                'currencycode' => $mer_array['currency'],
                'schemecode' => $mer_array['merchantSchemeCode'],
                'consumerId' => $consumerId,
                'mobileNumber' => $mobile,
                'email' => $email,
                'customerName' => $student->student_name ?? '',
                'accNo' => '',
                'accountName' => '',
                'aadharNumber' => '',
                'ifscCode' => '',
                'accountType' => '',
                'debitStartDate' => '',
                'debitEndDate' => '',
                'maxAmount' => '',
                'amountType' => '',
                'frequency' => '',
                'cardNumber' => '',
                'expMonth' => '',
                'expYear' => '',
                'cvvCode' => '',
                'hash' => $hashVal
            );

            // Create transaction record
            $transaction = $examForm->transactions()->create([
                'transaction_id' => $txnId,
                'merchant_transaction_id' => $txnId,
                'consumer_id' => $consumerId,
                'amount' => $amount,
                'currency' => $mer_array['currency'],
                'status' => 'initiated',
                'gateway' => 'worldline',
                'request_data' => [
                    'payment_request' => $paymentDetails,
                    'merchant_config' => $mer_array,
                    'data_string' => $datastring
                ],
                'hash' => $hashVal,
                'customer_name' => $student->student_name ?? '',
                'customer_email' => $email,
                'customer_mobile' => $mobile,
                'metadata' => [
                    'merchant_id' => $mer_array['merchantCode'],
                    'scheme_code' => $mer_array['merchantSchemeCode']
                ]
            ]);

            session()->put('transaction', $transaction);
            DB::commit();

            return redirect()->route('student.payment.checkout', ['transactionId' => $txnId]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return back()->with('error', 'Payment initiation failed: ' . $e->getMessage());
        }
    }

    public function showCheckout($transactionId)
    {
        try {
            $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();
            $mer_array = json_decode(file_get_contents(storage_path() . "/json/worldline_paymentgateway.json"), true);

            $payval = [
                'merchantId' => $mer_array['merchantCode'],
                'txnId' => $transaction->transaction_id,
                'amount' => $transaction->amount,
                'currencycode' => $mer_array['currency'],
                'schemecode' => $mer_array['merchantSchemeCode'],
                'consumerId' => $transaction->consumer_id,
                'mobileNumber' => $transaction->customer_mobile ?? '8896287276',
                'email' => $transaction->customer_email ?? 'info@witlnmu.ac.in',
                'customerName' => $transaction->customer_name,
                'accNo' => '',
                'accountName' => '',
                'aadharNumber' => '',
                'ifscCode' => '',
                'accountType' => '',
                'debitStartDate' => '',
                'debitEndDate' => '',
                'maxAmount' => '',
                'amountType' => '',
                'frequency' => '',
                'cardNumber' => '',
                'expMonth' => '',
                'expYear' => '',
                'cvvCode' => '',
                'hash' => $transaction->hash,
            ];
            // dd($payval);
            return view('payment.checkoutpage', compact('payval', 'mer_array', 'transaction'));

        } catch (\Exception $e) {
            Log::error('Checkout page failed', ['transaction_id' => $transactionId, 'error' => $e->getMessage()]);
            return back()->with('error', 'Unable to load checkout page: ' . $e->getMessage());
        }
    }

    /**
     * AJAX Status update from checkout page
     */
    public function updateAjaxStatus(Request $request)
    {
        try {
            $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();
            if ($transaction) {
                $currentMetadata = is_array($transaction->metadata) ? $transaction->metadata : [];
                $eventData = json_decode($request->data, true) ?? [];

                $ajaxEvents = $currentMetadata['ajax_events'] ?? [];
                $ajaxEvents[] = array_merge($eventData, ['status' => $request->status, 'server_time' => now()->toDateTimeString()]);

                $currentMetadata['ajax_events'] = $ajaxEvents;

                $transaction->update([
                    'status' => $request->status,
                    'metadata' => $currentMetadata
                ]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('AJAX status update failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function handlePaymentResponse(Request $request)
    {
        try {
            $response = $request->msg;
            $res_msg = explode("|", $response);

            if (count($res_msg) < 6) {
                Log::error('Invalid payment response format', ['response' => $response]);
                return redirect()->route('student.payment.failed')->with('error', 'Invalid payment response');
            }

            $merchantTxnId = $res_msg[3];
            $transaction = Transaction::where('merchant_transaction_id', $merchantTxnId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for response', ['txn_id' => $merchantTxnId]);
                return redirect()->route('student.payment.failed')->with('error', 'Transaction not found');
            }

            if (in_array($transaction->status, ['success', 'failed', 'completed'])) {
                return redirect()->route('student.payment.' . $transaction->status)->with('transaction', $transaction);
            }

            $transaction->update([
                'status' => 'processing',
                'response_data' => array_merge($transaction->response_data ?? [], ['gateway_response' => $res_msg])
            ]);

            $path = storage_path() . "/json/worldline_paymentgateway.json";
            $mer_array = json_decode(file_get_contents($path), true);
            date_default_timezone_set('Asia/Calcutta');
            $strCurDate = date('d-m-Y');

            $arr_req = [
                "merchant" => ["identifier" => $mer_array['merchantCode']],
                "transaction" => [
                    "deviceIdentifier" => "S",
                    "currency" => $mer_array['currency'],
                    "dateTime" => $strCurDate,
                    "token" => $res_msg[5],
                    "requestType" => "S"
                ]
            ];

            $finalJsonReq = json_encode($arr_req);
            $method = 'POST';
            $url = "https://www.paynimo.com/api/paynimoV2.req";
            $res_result = $this->callAPI($method, $url, $finalJsonReq);
            $dualVerifyData = json_decode($res_result, true);

            $statusCode = $dualVerifyData['paymentMethod']['paymentTransaction']['statusCode'] ?? $res_msg[0];

            if ($statusCode === '0300') {
                $finalStatus = 'success';
            } elseif (in_array($statusCode, ['0398', '0399'])) {
                $finalStatus = 'failed';
            } else {
                $finalStatus = 'pending';
            }

            $transaction->update([
                'status' => $finalStatus,
                'gateway_transaction_id' => $res_msg[5] ?? null,
                'response_data' => array_merge($transaction->response_data ?? [], [
                    'dual_verify_request' => $arr_req,
                    'dual_verify_response' => $dualVerifyData,
                    'final_status_code' => $statusCode,
                    'processed_at' => now()
                ])
            ]);

            Log::info('Transaction verification completed', [
                'transaction_id' => $merchantTxnId,
                'final_status' => $finalStatus
            ]);

            if ($finalStatus === 'success') {
                $transaction->transactionable->update(['payment_status' => 'done']);
            }

            return redirect()->route('student.payment.' . $finalStatus)->with(['transaction' => $transaction]);

        } catch (\Exception $e) {
            Log::error('Payment response processing failed', ['error' => $e->getMessage()]);
            return redirect()->route('student.payment.failed')->with('error', 'Payment processing failed');
        }
    }

    public function success()
    {
        $transaction = session('transaction');
        return view('payment.success', compact('transaction'));
    }

    public function failed()
    {
        $transaction = session('transaction');
        return view('payment.failed', compact('transaction'));
    }

    public function pending()
    {
        $transaction = session('transaction');
        return view('payment.pending', compact('transaction'));
    }

    public function recheckStatus($transactionId)
    {
        try {
            $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();

            if (in_array($transaction->status, ['success', 'completed'])) {
                return response()->json(['success' => true, 'message' => 'Transaction already successful', 'status' => $transaction->status]);
            }

            $path = storage_path() . "/json/worldline_paymentgateway.json";
            $mer_array = json_decode(file_get_contents($path), true);
            date_default_timezone_set('Asia/Calcutta');
            $strCurDate = date('d-m-Y');

            $arr_req = [
                "merchant" => ["identifier" => $mer_array['merchantCode']],
                "transaction" => [
                    "deviceIdentifier" => "S",
                    "currency" => $mer_array['currency'],
                    "dateTime" => $strCurDate,
                    "identifier" => $transaction->transaction_id,
                    "requestType" => "S"
                ]
            ];

            $finalJsonReq = json_encode($arr_req);
            $method = 'POST';
            $url = "https://www.paynimo.com/api/paynimoV2.req";
            $res_result = $this->callAPI($method, $url, $finalJsonReq);
            $dualVerifyData = json_decode($res_result, true);

            $statusCode = $dualVerifyData['paymentMethod']['paymentTransaction']['statusCode'] ?? 'failed';

            if ($statusCode === '0300') {
                $finalStatus = 'success';
            } elseif (in_array($statusCode, ['0398', '0399'])) {
                $finalStatus = 'failed';
            } else {
                $finalStatus = 'pending';
            }

            $transaction->update([
                'status' => $finalStatus,
                'response_data' => array_merge($transaction->response_data ?? [], [
                    'recheck_verify_request' => $arr_req,
                    'recheck_verify_response' => $dualVerifyData,
                    'recheck_at' => now()
                ])
            ]);

            if ($finalStatus === 'success') {
                $transaction->transactionable->update(['payment_status' => 'done']);
            }

            return response()->json([
                'success' => true,
                'status' => $finalStatus,
                'message' => 'Status updated: ' . ucfirst($finalStatus)
            ]);

        } catch (\Exception $e) {
            Log::error('Status recheck failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Recheck failed: ' . $e->getMessage()], 500);
        }
    }

    private function callAPI($method, $url, $finalJsonReq)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);
                break;
            default:
                if ($finalJsonReq)
                    $url = sprintf("%s?%s", $url, http_build_query($finalJsonReq));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
