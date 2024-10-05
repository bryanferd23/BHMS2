<?php

namespace App\Http\Controllers;

use App\Models\Boarder;
use App\Models\Payment;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index()
    {
        $boarders = Boarder::query()->orderByDesc('created_at')->paginate(10);

        foreach ($boarders as $boarder) {
            $boarder->total = $boarder->payments->sum('amount');
            $boarder->balance = $boarder->created_at->diffInDays(now()) * 100 - $boarder->total;
        }

        return view('balance.index', ['boarders' => $boarders]);
    }

    public function search(Request $request)
    {
        $boarders = Boarder::query()
        ->where(function ($query) use ($request) {
            $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
        })
        ->orderBy('id')
        ->paginate(10);
        
        foreach ($boarders as $boarder) {
            $boarder->total = $boarder->payments->sum('amount');
            $boarder->balance = (int)($boarder->created_at->diffInDays(now()) * 100) - $boarder->total;
        }
        return view('balance.index', ['boarders' => $boarders]);
    }
}
