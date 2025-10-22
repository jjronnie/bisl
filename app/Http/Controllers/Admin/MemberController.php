<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Member; // Assuming you have a Member model
use App\Models\SavingsAccount; // Assuming you have a SavingsAccount model
use App\Mail\TemporaryPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;



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
            'has_existing_savings' => 'nullable|boolean',
            'existing_savings_details' => 'nullable|string',
            'is_currently_in_debt' => 'nullable|boolean',
            'debt_details' => 'nullable|string',
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
                    'must_change_password' => true, // Force password change on first login
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
                    'member_no' => 'BISL' . now()->year . strtoupper(Str::random(5)), // Custom Member No.
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
                    'has_existing_savings' => $validated['has_existing_savings'] ?? false,
                    'existing_savings_details' => $validated['existing_savings_details'] ?? null,
                    'is_currently_in_debt' => $validated['is_currently_in_debt'] ?? false,
                    'debt_details' => $validated['debt_details'] ?? null,
                ]);

                // 2.6. Create Default Savings Account
                SavingsAccount::create([
                    'member_id' => $member->id,
                    'account_number' => 'ACC-' . now()->year . '-' . strtoupper(Str::random(6)), // Custom Account No.
                    'balance' => 0,
                    'status' => 'active',
                ]);

                // 2.7. Send Temporary Password Email (NO QUEUEING - immediate send)
                // We send it here, at the end of the transaction, so it only goes out if DB commit is successful.
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }
}
