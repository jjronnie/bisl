<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TierHelper;
use App\Http\Controllers\Controller;
use App\Mail\TemporaryPasswordMail;
use App\Models\InterestLedger;
use App\Models\Member;
use App\Models\SaccoAccount;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('user', 'savingsAccount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone1', 'like', "%{$search}%")
                    ->orWhereHas('savingsAccount', function ($q) use ($search) {
                        $q->where('account_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        $members = $query->latest()->paginate(20);

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

            'opening_balance' => 'nullable|numeric|min:0',
            'membership_fee' => 'nullable|numeric|min:0',
            'loan_protection_fund' => 'nullable|numeric|min:0',

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

                $user->forceFill(['email_verified_at' => now()])->save();

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
                    'membership_fee' => $validated['membership_fee'] ?? 0,
                    'loan_protection_fund' => $validated['loan_protection_fund'] ?? 0,
                    'status' => 'active',
                ]);

                TierHelper::updateTier($member);

                $saccoAccount = SaccoAccount::first();
                if (! $saccoAccount) {
                    return back()->with('error', 'SACCO operational account not found. Cannot log payment.');
                }
                $saccoAccount->member_savings += $validated['opening_balance'];
                $saccoAccount->loan_protection_fund += $validated['loan_protection_fund'];
                $saccoAccount->operational += $validated['membership_fee'];
                $saccoAccount->save();

                Mail::to($user->email)->send(new TemporaryPasswordMail($user, $plainPassword));

            });
        } catch (\Exception $e) {
            // Handle mail failure or any other transaction error
            if ($e instanceof RoleDoesNotExist) {
                Log::error('Role "user" not found: '.$e->getMessage());

                return back()->with('error', 'User creation failed: The required default role is missing.')->withInput();
            }

            // Log the error for debugging
            Log::error('User creation transaction failed: '.$e->getMessage());

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
        $accessible = $balance;

        return view('admin.members.show', compact('member', 'balance', 'loanProtection', 'accessible'));
    }

    public function transactions(Member $member)
    {

        $transactions = $member->transactions()->latest()
            ->paginate(20);

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
            'email' => 'required|email|unique:users,email,'.$member->user_id, // Exclude current user's ID
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'national_id_number' => 'nullable|string|unique:members,national_id_number,'.$member->id, // Exclude current member's ID
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

                if (! $user) {
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
            Log::error('Member update transaction failed for ID '.$member->id.': '.$e->getMessage());

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
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        try {
            DB::transaction(function () use ($member) {
                $user = $member->user;

                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }

                $member->delete();

                if ($user) {
                    $user->delete();
                }
            });
        } catch (\Exception $e) {
            Log::error('Member deletion failed for ID '.$member->id.': '.$e->getMessage());

            return back()->with('error', 'Member deletion failed due to a system error.');
        }

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }

    public function suspend(Member $member)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        $user = $member->user;

        if (! $user) {
            return back()->with('error', 'Associated user account not found.');
        }

        if ($user->status === 'suspended') {
            return back()->with('error', 'Member account is already suspended.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', 'Member account has been suspended.');
    }

    public function unsuspend(Member $member)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403);

        $user = $member->user;

        if (! $user) {
            return back()->with('error', 'Associated user account not found.');
        }

        if ($user->status === 'active') {
            return back()->with('error', 'Member account is already active.');
        }

        $user->update(['status' => 'active']);

        return back()->with('success', 'Member account has been reactivated.');
    }

    public function interest(Request $request)
    {
        $query = InterestLedger::with('member');

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $ledgers = $query->latest()->paginate(25);

        $members = Member::orderBy('name')->get();

        $years = InterestLedger::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $lastUpdated = InterestLedger::latest()->value('created_at');

        return view('admin.members.interest', compact('ledgers', 'members', 'years', 'lastUpdated'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $members = Member::with('savingsAccount')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhereHas('savingsAccount', function ($q) use ($query) {
                        $q->where('account_number', 'like', "%{$query}%");
                    });
            })
            ->limit(10)
            ->get();

        $results = $members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'account_number' => $member->savingsAccount?->account_number ?? 'N/A',
                'savings_balance' => $member->savingsAccount?->balance ?? 0,
                'lpf_balance' => $member->savingsAccount?->loan_protection_fund ?? 0,
            ];
        });

        return response()->json($results);
    }

    public function updateMonthlyInterest()
    {
        // Define interest rates
        $goldRate = 0.10;   // 10% monthly
        $silverRate = 0.01; // 1% monthly
        $goldThreshold = 1_000_000; // 1 million UGX

        // Get current month and year to prevent duplicates
        $currentMonth = now()->format('Y-m');

        $members = Member::with('savingsAccount')->get();

        DB::transaction(function () use ($members, $goldRate, $silverRate, $goldThreshold, $currentMonth) {
            foreach ($members as $member) {
                // Check if interest already calculated this month
                $existingRecord = DB::table('interest_ledger')
                    ->where('member_id', $member->id)
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
                    ->exists();

                if ($existingRecord) {
                    continue; // Skip this member, already processed this month
                }

                $savings = $member->savingsAccount;
                $balance = $savings->balance;
                $oldInterestEarned = $savings->interest_earned;

                // Determine tier based on balance
                $tier = $balance >= $goldThreshold ? 'gold' : 'silver';
                $rate = $tier === 'gold' ? $goldRate : $silverRate;

                // Calculate interest on CURRENT BALANCE only
                $interest = $balance * $rate;
                $newInterestEarned = $oldInterestEarned + $interest;

                // Update the accumulated interest
                $savings->update([
                    'interest_earned' => $newInterestEarned,
                ]);

                // Record in ledger
                DB::table('interest_ledger')->insert([
                    'member_id' => $member->id,
                    'balance_before' => $balance, // This should be the savings balance, not old interest
                    'interest_amount' => $interest,
                    'balance_after' => $balance, // Balance doesn't change, only interest accumulates
                    'tier' => $tier,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update SACCO total interest ONCE after all members
            $sacco = SaccoAccount::first();
            if ($sacco) {
                $totalInterest = DB::table('interest_ledger')
                    ->sum('interest_amount');

                $sacco->update([
                    'member_interest' => $totalInterest,
                ]);
            }
        });

        return back()->with('success', 'Interest updated for all members based on current balances.');
    }
}
