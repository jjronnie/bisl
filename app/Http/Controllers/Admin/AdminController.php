<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::role('admin')->latest()->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admins.create');
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    // Suspend user
public function suspend(User $admin)
{
    // Prevent self-suspension if needed
    if (auth()->id() === $admin->id) {
        return back()->with('error', 'You cannot suspend your own account.');
    }

    $admin->update([
        'status' => 'suspended',
    ]);

    return back()->with('success', 'User suspended successfully.');
}

// Unsuspend user
public function unsuspend(User $admin)
{
    $admin->update([
        'status' => 'active',
    ]);

    return back()->with('success', 'User restored successfully.');
}

}
