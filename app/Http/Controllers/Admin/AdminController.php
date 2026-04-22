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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::role(['admin', 'superadmin'])->latest()->get();

        return view('admin.admins.index', compact('admins'));
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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:6048',
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                $plainPassword = (string) random_int(100000, 999999);

                $profilePhotoPath = null;
                $file = $request->file('profile_photo');
                if ($file) {
                    $profilePhotoPath = $file->store('avatars/admins', 'public');
                }

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'status' => 'active',
                    'password' => Hash::make($plainPassword),
                    'must_change_password' => true,
                    'created_by' => Auth::user()->id,
                    'profile_photo' => $profilePhotoPath,
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
    public function edit(User $admin)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        $roles = Role::whereIn('name', ['admin', 'superadmin'])->get();

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'status' => 'required|in:active,suspended',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:6048',
        ]);

        $profilePhotoPath = $admin->profile_photo;

        $file = $request->file('profile_photo');
        if ($file) {
            if ($admin->profile_photo) {
                Storage::disk('public')->delete($admin->profile_photo);
            }
            $profilePhotoPath = $file->store('avatars/admins', 'public');
        }

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'profile_photo' => $profilePhotoPath,
        ]);

        $admin->syncRoles($validated['roles']);

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        // Prevent deleting superadmin
        if ($admin->hasRole('superadmin')) {
            return back()->with('error', 'Superadmin accounts cannot be deleted.');
        }

        if (auth()->id() === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $admin->name;
        $admin->delete();

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
