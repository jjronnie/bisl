<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminInviteMail;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',

        ]);

        // 2. DATABASE TRANSACTION (Ensures atomic operations)
        try {
            DB::transaction(function () use ($validated, $request) {

                // 1. Generate a 6-digit numeric temporary password (OTP style)
                $plainPassword = (string) random_int(100000, 999999);


                // 2.2. Create User Record
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
           
                    'status' => 'active',
                    'password' => Hash::make($plainPassword),
                    'must_change_password' => true,
                    'created_by' => Auth::user()->id,
                ]);

                  // 2.3. Assign Role (Ensure the 'user' role exists)
                $adminRole = Role::where('name', 'admin')->firstOrFail();
                $user->assignRole($adminRole);

                Mail::to($user->email)->send(new AdminInviteMail($user, $plainPassword));

            });
        } catch (\Exception $e) {

            // Log the error for debugging
            Log::error('User creation transaction failed: ' . $e->getMessage());

            // A generic, user-friendly error response
            return back()->with('error', 'Invite failed due to a system error. Please check logs.')->withInput();
        }

        // 3. REDIRECTION
        return redirect()->route('admin.admins.index')->with('success', 'Admin Invited successfully. Temporary credentials have been emailed.');
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
    // Block suspending superadmins
    if ($admin->hasRole('superadmin')) {
        return back()->with('error', 'Superadmin accounts cannot be suspended.');
    }

    // Block suspending yourself
    if (auth()->id() === $admin->id) {
        return back()->with('error', 'You cannot suspend your own account.');
    }

    // Update status safely
    $admin->update([
        'status' => 'suspended',
    ]);

    return back()->with('success', 'User suspended successfully.');
}

public function unsuspend(User $admin)
{
    // Optional: also block unsuspending superadmins if status logic should never touch them
    if ($admin->hasRole('superadmin')) {
        return back()->with('error', 'Superadmin accounts cannot be modified.');
    }

    $admin->update([
        'status' => 'active',
    ]);

    return back()->with('success', 'User restored successfully.');
}


}
