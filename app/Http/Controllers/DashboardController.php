<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('members.dashboard');
    }

       public function adminDashboard()
    {
        return view('admin.dashboard');
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
