<?php

namespace App\Http\Controllers;

use App\Models\Boarder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BoarderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boarders = Boarder::orderByDesc('created_at')->paginate(10);
        return view('boarder.index', compact('boarders'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:boarders',
            'phone' => 'required',
            'address' => 'required',
        ]);

        Boarder::create($validate);
        Session::flash('message', 'Created successfully');
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $boarderId)
    {

        $validate = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'status' => 'required',
        ]);
    
        $boarder = Boarder::find($boarderId);
        $boarder->update($validate);

        Session::flash('message', 'Updated successfully');
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $boarder = Boarder::find($request->boarder_id);
        
        $boarder->delete();
        Session::flash('message', 'Deleted successfully');
        return response()->json(['status' => 'success'], 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $boarders = Boarder::where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')->orderByDesc('created_at')->paginate(10);
        return view('boarder.index', compact('boarders'));
        
    }
}
