<?php

namespace App\Http\Controllers;

use App\Models\Boarder;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('boarder')->orderByDesc('created_at')->paginate(10);

        return view('payment.index', compact('payments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
     public function store(Request $request)
     {
         $validate = $request->validate([
             'amount' => 'required|gt:0',
             'email' => 'required|email|exists:boarders,email',
         ]);
         
         $boarder = Boarder::where('email', $request->email)->first();

         Payment::create(['amount' => $validate['amount'], 'boarder_id' => $boarder->id]);
         Session::flash('message', 'Created successfully');
         return response()->json(['status' => 'success'], 200);
     }
 
     /**
      * Update the specified resource in storage.
      */
     public function update(Request $request, $paymentId)
     {
         $validate = $request->validate([
             'amount' => 'required|gt:0',
             'email' => 'required|email|exists:boarders,email',
         ]);

         $boarder = Boarder::where('email', $request->email)->first();
     
         $payment = payment::find($paymentId);
         $payment->update(['amount' => $validate['amount'], 'boarder_id' => $boarder->id]);
 
         Session::flash('message', 'Updated successfully');
         return response()->json(['status' => 'success'], 200);
     }
 
     /**
      * Remove the specified resource from storage.
      */
     public function destroy(Request $request)
     {
         $payment = payment::find($request->payment_id);
         
         $payment->delete();
         Session::flash('message', 'Deleted successfully');
         return response()->json(['status' => 'success'], 200);
     }
 
     public function search(Request $request)
     {
         $search = $request->search;

         $payments = Payment::with('boarder')
         ->whereHas('boarder', function ($query) use ($search) {
             $query->where('name', 'like', '%' . $search . '%')
             ->orWhere('email', 'like', '%' . $search . '%');
         })->orderByDesc('created_at')->paginate(10);

         
         return view('payment.index', compact('payments'));
     }
}
