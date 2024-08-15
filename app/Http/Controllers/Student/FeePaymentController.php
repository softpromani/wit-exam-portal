<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PaymentTracking;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    // public function studentfeepayment(Request $request){
    //     // dd($request->all());
    //     // Validate the form data
    //          $studentfee = $request->validate([
    //         'form_id'=>'required|exists:exam_forms,id',
    //         'payment_status' => 'required|string|in:paid,unpaid,partial',
    //         'payment_mode' => 'required|string|in:online,cash',
    //         'amount' => 'required|numeric|min:0',
    //         'transaction_id' => 'nullable|string|max:255',
    //     ]);

    //     $studentpayment=PaymentTracking::create($studentfee);


    //     // Redirect back with success message
    //     return redirect()->back()->with('success', 'Payment recorded successfully.');
    //   }
}
