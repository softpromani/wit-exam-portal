<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use App\Models\Payment;
use App\Models\PaymentTracking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function feePayment(Request $request){
        //  dd($request->all());
        // Validate the form data
        $validatedData = $request->validate([
            'form_id'=>'required|exists:exam_forms,id',
            'payment_status' => 'required|string|in:paid,unpaid,partial',
            'payment_mode' => 'required|string|in:online,cash',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
        ]);

    $feepay=ExamForm::find($validatedData['form_id'])->payment()->create([
        'total_amount'=>$validatedData['amount'],
        'paid_amount'=>$validatedData['amount'],
        'fine_amount'=>0.00,
        'due_amount'=>0.00,
        'payment_status'=>$validatedData['payment_status']
    ]);

    $studentpayment=PaymentTracking::create([
        'payment_id'=>$feepay->id,
        'payment_status'=>'success',
        'payment_mode'=>$validatedData['payment_mode'],
        'amount'=>$validatedData['amount'],
        'transaction_id'=>$validatedData['transaction_id'],
    ]);


        // Redirect back with success message
        return redirect()->back()->with('success', 'Payment recorded successfully.');
      }


}
