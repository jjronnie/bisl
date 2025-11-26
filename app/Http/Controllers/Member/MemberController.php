<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

           $member = auth()->user()->member;

        if (!$member) {
            abort(404, 'Member account not found');
        }

        
        // transactions from member
        $transactions = $member->transactions()->latest()
            ->paginate(20);
        
        return view('members.transactions', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function notifications()
    {
         return view('members.notifications');
    }

 public function loans()
{
    $member = auth()->user()->member;

    if (!$member) {
        abort(404, 'Member account not found');
    }

    $loans = $member->loans()
        ->latest()
        ->paginate(10);

    $loanCount =  $member->loans()->count();

    $pendingCount = $member->loans()->where('status', 'pending')->count();
    $rejectedCount = $member->loans()->where('status', 'rejected')->count();
    $completedCount = $member->loans()->where('status', 'completed')->count();

    return view('members.loans', compact(
        'loans',
        'loanCount',
        'pendingCount',
        'rejectedCount',
        'completedCount'
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
