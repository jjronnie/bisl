<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminInviteMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::role(['admin', 'superadmin'])->latest()->get();
        $roles = Role::whereIn('name', ['admin', 'superadmin'])->get();

        return view('admin.admins.index', compact('admins', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'superadmin'])->get();

        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $plainPassword = (string) random_int(100000, 999999);

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'status' => 'active',
                    'password' => Hash::make($plainPassword),
                    'must_change_password' => true,
                    'created_by' => Auth::user()->id,
                ]);

                $user->forceFill(['email_verified_at' => now()])->save();

                $user->syncRoles($validated['roles']);

                Mail::to($user->email)->send(new AdminInviteMail($user, $plainPassword));
            });
        } catch (\Exception $e) {
            Log::error('User creation transaction failed: '.$e->getMessage());

            return back()->with('error', 'Invite failed due to a system error. Please check logs.')->withInput();
        }

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
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        // Prevent editing superadmin
        if ($user->hasRole('superadmin')) {
            abort(403, 'Superadmin accounts cannot be edited.');
        }

        $roles = Role::whereIn('name', ['admin', 'superadmin'])->get();

        return view('admin.admins.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        // Prevent editing superadmin
        if ($user->hasRole('superadmin')) {
            abort(403, 'Superadmin accounts cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'status' => 'required|in:active,suspended',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        // Prevent deleting superadmin
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'Superadmin accounts cannot be deleted.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.admins.index')->with('success', "Admin {$name} deleted successfully.");
    }

    // Suspend user
    public function suspend(User $admin)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        if ($admin->hasRole('superadmin')) {
            return back()->with('error', 'Superadmin accounts cannot be suspended.');
        }

        if (auth()->id() === $admin->id) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $admin->update([
            'status' => 'suspended',
        ]);

        return back()->with('success', 'User suspended successfully.');
    }

    public function unsuspend(User $admin)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        if ($admin->hasRole('superadmin')) {
            return back()->with('error', 'Superadmin accounts cannot be modified.');
        }

        $admin->update([
            'status' => 'active',
        ]);

        return back()->with('success', 'User restored successfully.');
    }
}
