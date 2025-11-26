<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Mail\TemporaryPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;



class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('user')->latest()->paginate(50);
        return view('admin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'national_id_number' => 'nullable|string|unique:members,national_id_number', // Adjusted table name
            'passport_number' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'interest_rate' => 'nullable|numeric',
            'loan_protection_fund' => 'nullable|numeric',



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
                    'password' => Hash::make($plainPassword),
                    'must_change_password' => true,
                    'created_by' => Auth::user()->id,
                ]);

                // 2.3. Assign Role (Ensure the 'user' role exists)
                $userRole = Role::where('name', 'user')->firstOrFail();
                $user->assignRole($userRole);

                // 2.4. Handle Avatar Upload
                $avatarPath = null;
                if ($request->hasFile('avatar')) {
                    // Store the avatar and get the path
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                }

                // 2.5. Create Member Record
                $member = Member::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'nationality' => $validated['nationality'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'marital_status' => $validated['marital_status'] ?? null,
                    'national_id_number' => $validated['national_id_number'] ?? null,
                    'passport_number' => $validated['passport_number'] ?? null,
                    'avatar' => $avatarPath,
                    'phone1' => $validated['phone1'],
                    'phone2' => $validated['phone2'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);

                // 2.6. Create Default Savings Account
                SavingsAccount::create([
                    'member_id' => $member->id,
                    'account_number' => generateAccountNumber(),
                    'balance' => $validated['opening_balance'] ?? 0,
                    'interest_rate' => $validated['interest_rate'] ?? 0,
                    'loan_protection_fund' => $validated['loan_protection_fund'] ?? 0,
                    'status' => 'active',
                ]);




                Mail::to($user->email)->send(new TemporaryPasswordMail($user, $plainPassword));

            });
        } catch (\Exception $e) {
            // Handle mail failure or any other transaction error
            if ($e instanceof \Spatie\Permission\Exceptions\RoleDoesNotExist) {
                Log::error('Role "user" not found: ' . $e->getMessage());
                return back()->with('error', 'User creation failed: The required default role is missing.')->withInput();
            }

            // Log the error for debugging
            Log::error('User creation transaction failed: ' . $e->getMessage());

            // A generic, user-friendly error response
            return back()->with('error', 'User creation failed due to a system error. Please check logs.')->withInput();
        }

        // 3. REDIRECTION
        return redirect()->route('admin.members.index')->with('success', 'Member created successfully. Temporary credentials have been emailed.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {

        $savingsAccount = $member->savingsAccount;

        // balance from savings account
        $balance = $savingsAccount?->balance ?? 0;
        $loanProtection = $savingsAccount?->loan_protection_fund ?? 0;

        // accessible amount
        $accessible = (float) $balance + (float) $loanProtection;

        return view('admin.members.show', compact('member', 'balance', 'loanProtection', 'accessible'));
    }


    public function transactions(Member $member)
    {

       
        $transactions = $member->transactions()->latest()
            ->paginate(2);

        return view('admin.members.transactions', compact('transactions', 'member'));
    }







    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        // 1. VALIDATION
        // We must ignore the current user's email and current member's national ID for uniqueness checks.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->user_id, // Exclude current user's ID
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'national_id_number' => 'nullable|string|unique:members,national_id_number,' . $member->id, // Exclude current member's ID
            'passport_number' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048', // Allow new file upload
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'address' => 'nullable|string',

        ]);

        // 2. DATABASE TRANSACTION
        try {
            DB::transaction(function () use ($validated, $request, $member) {
                // Ensure the member has an associated user
                $user = $member->user;

                if (!$user) {
                    throw new \Exception("Associated user not found for member ID {$member->id}");
                }

                // 2.1. Update User Record (Name and Email)
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    // Password and role are intentionally unchanged
                ]);

                // 2.2. Handle Avatar Update and Deletion of Old File
                $avatarPath = $member->avatar; // Keep existing path by default

                if ($request->hasFile('avatar')) {
                    // Delete old avatar if it exists
                    if ($member->avatar) {
                        Storage::disk('public')->delete($member->avatar);
                    }
                    // Store the new avatar and get the path
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                }

                // 2.3. Prepare Member data for update
                $memberData = [
                    'name' => $validated['name'],
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'nationality' => $validated['nationality'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'marital_status' => $validated['marital_status'] ?? null,
                    'national_id_number' => $validated['national_id_number'] ?? null,
                    'passport_number' => $validated['passport_number'] ?? null,
                    'avatar' => $avatarPath, // New or old path
                    'phone1' => $validated['phone1'],
                    'phone2' => $validated['phone2'] ?? null,
                    'address' => $validated['address'] ?? null,



                ];

                // 2.4. Update Member Record
                $member->update($memberData);

                // No email sending
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Member update transaction failed for ID ' . $member->id . ': ' . $e->getMessage());

            // A generic, user-friendly error response
            return back()->with('error', 'Member update failed due to a system error. Please check logs.')->withInput();
        }

        // 3. REDIRECTION
        return redirect()->route('admin.members.index')->with('success', 'Member details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        try {
            DB::transaction(function () use ($member) {
                $user = $member->user;

                // 1. Delete Avatar file from storage before deleting the member record
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }

                // 2. Delete Member record (Assuming soft delete is supported by the model)
                $member->delete();

                // 3. Delete associated User record
                if ($user) {
                    // Assuming soft delete is supported by the User model
                    $user->delete();
                }
            });
        } catch (\Exception $e) {
            Log::error('Member deletion failed for ID ' . $member->id . ': ' . $e->getMessage());
            return back()->with('error', 'Member deletion failed due to a system error.');
        }

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }



}
