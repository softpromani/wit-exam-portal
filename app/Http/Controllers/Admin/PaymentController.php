<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use App\Models\Payment;
use App\Models\PaymentTracking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function feePayment(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'form_id' => 'required|exists:exam_forms,id',
            'payment_status' => 'required|string|in:paid,unpaid,partial',
            'payment_mode' => 'required|string|in:online,cash,upi,card,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $examForm = ExamForm::findOrFail($validatedData['form_id']);

        $feepay = $examForm->payment()->updateOrCreate(
            ['paymentable_id' => $examForm->id, 'paymentable_type' => ExamForm::class],
            [
                'total_amount' => $validatedData['amount'],
                'paid_amount' => $validatedData['amount'],
                'fine_amount' => 0.00,
                'due_amount' => 0.00,
                'payment_status' => $validatedData['payment_status']
            ]
        );

        $studentpayment = PaymentTracking::create([
            'payment_id' => $feepay->id,
            'payment_status' => 'success',
            'payment_mode' => $validatedData['payment_mode'],
            'amount' => $validatedData['amount'],
            'transaction_id' => $validatedData['transaction_id'],
        ]);

        // Create a Transaction record for unification
        $examForm->transactions()->create([
            'transaction_id' => $validatedData['transaction_id'] ?? ('OFF' . time() . rand(100, 999)),
            'merchant_transaction_id' => $validatedData['transaction_id'] ?? null,
            'amount' => $validatedData['amount'],
            'currency' => 'INR',
            'status' => 'success',
            'gateway' => 'offline',
            'payment_method' => $validatedData['payment_mode'],
            'customer_name' => $examForm->student->student_name ?? 'Student',
            'customer_email' => $examForm->student->email ?? '',
            'customer_mobile' => $examForm->student->mobile_number ?? '',
            'metadata' => [
                'recorded_by' => auth()->user()->id,
                'recorded_at' => now()->toDateTimeString(),
                'payment_mode' => $validatedData['payment_mode']
            ]
        ]);

        // Update ExamForm payment_status
        if ($validatedData['payment_status'] === 'paid') {
            $examForm->update(['payment_status' => 'done']);
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Payment recorded and synced successfully.');
    }


}
