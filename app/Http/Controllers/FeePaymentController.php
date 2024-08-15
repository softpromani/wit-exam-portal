<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentTracking;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
  public function studentfeepayment(Request $request){
    // dd($request->all());
    // Validate the form data
    $validatedData = $request->validate([
        'form_id'=>'required|exists:exam_forms,id',
        'payment_status' => 'required|string|in:paid,unpaid,partial',
        'payment_mode' => 'required|string|in:online,cash',
        'amount' => 'required|numeric|min:0',
        'transaction_id' => 'nullable|string|max:255',
    ]);

    

    // Redirect back with success message
    return redirect()->back()->with('success', 'Payment recorded successfully.');
  }
}
