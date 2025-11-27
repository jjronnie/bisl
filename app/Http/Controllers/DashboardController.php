<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\SaccoAccount;



class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Redirect Admins to their dashboard
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('user')) {
            // Redirect Members to their dashboard
            return redirect()->route('member.dashboard');
        }

        // Default view for users without a specific role
        return view('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function memberDashboard()
    {
        $member = auth()->user()->member;

        if (!$member) {
            abort(404, 'Member account not found');
        }

        $savingsAccount = $member->savingsAccount;




        // balance from savings account
        $balance = $savingsAccount?->balance ?? 0;
        $loanProtection = $savingsAccount?->loan_protection_fund ?? 0;

        // accessible amount
        $accessible = (float) $balance + (float) $loanProtection;


        // transactions from member
        $transactions = $member->transactions()->latest()
            ->take(5)
            ->get();

        return view('members.dashboard', compact('balance', 'transactions', 'member', 'accessible', 'loanProtection'));
    }



    public function adminDashboard()
    {
        $totalMembers = Member::count();

        $goldMembers = Member::where('tier', 'gold')->count();
        $silverMembers = Member::where('tier', 'silver')->count();



        $saccoAccount = SaccoAccount::first();


        $transactions = Transaction::with('member', 'savingsAccount', )
            ->latest()
            ->take(5)
            ->get();


        return view('admin.dashboard', compact(
            'totalMembers',
            'transactions',
            'saccoAccount',
            'goldMembers',
            'silverMembers'


        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
