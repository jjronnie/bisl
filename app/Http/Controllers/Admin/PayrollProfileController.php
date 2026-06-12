<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PayrollGrade;
use App\Models\PayrollProfile;
use Illuminate\Http\Request;

class PayrollProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollProfile::with(['member', 'payrollGrade']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_number', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $profiles = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payroll.profiles.index', compact('profiles'));
    }

    public function create()
    {
        $members = Member::with('savingsAccount')
            ->whereNull('deleted_at')
            ->whereDoesntHave('payrollProfile')
            ->orderBy('name')
            ->get();

        $grades = PayrollGrade::where('is_active', true)->orderBy('name')->get();

        return view('admin.payroll.profiles.create', compact('members', 'grades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id|unique:payroll_profiles,member_id',
            'payroll_grade_id' => 'required|exists:payroll_grades,id',
            'employee_number' => 'nullable|string|max:255|unique:payroll_profiles,employee_number',
            'employment_type' => 'required|in:permanent,part_time,casual',
            'qualification_level' => 'required|in:certificate,diploma,bachelors,masters,phd',
            'recognition_level' => 'required|in:none,appreciation,golden_medal',
            'meeting_allowance_eligible' => 'nullable|boolean',
            'employment_start_date' => 'required|date',
            'employment_end_date' => 'nullable|date|after:employment_start_date',
            'is_active' => 'nullable|boolean',
        ]);

        PayrollProfile::create($validated);

        return redirect()->route('admin.payroll.profiles.index')
            ->with('success', 'Payroll profile created successfully.');
    }

    public function show(PayrollProfile $profile)
    {
        $profile->load(['member', 'payrollGrade', 'payrollRuns' => function ($q) {
            $q->with('payrollPeriod')->latest();
        }]);

        return view('admin.payroll.profiles.show', compact('profile'));
    }

    public function edit(PayrollProfile $profile)
    {
        $grades = PayrollGrade::where('is_active', true)->orderBy('name')->get();

        return view('admin.payroll.profiles.edit', compact('profile', 'grades'));
    }

    public function update(Request $request, PayrollProfile $profile)
    {
        $validated = $request->validate([
            'payroll_grade_id' => 'required|exists:payroll_grades,id',
            'employee_number' => 'nullable|string|max:255|unique:payroll_profiles,employee_number,'.$profile->id,
            'employment_type' => 'required|in:permanent,part_time,casual',
            'qualification_level' => 'required|in:certificate,diploma,bachelors,masters,phd',
            'recognition_level' => 'required|in:none,appreciation,golden_medal',
            'meeting_allowance_eligible' => 'nullable|boolean',
            'employment_start_date' => 'required|date',
            'employment_end_date' => 'nullable|date|after:employment_start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $profile->update($validated);

        return redirect()->route('admin.payroll.profiles.index')
            ->with('success', 'Payroll profile updated successfully.');
    }

    public function destroy(PayrollProfile $profile)
    {
        $profile->delete();

        return redirect()->route('admin.payroll.profiles.index')
            ->with('success', 'Payroll profile deleted successfully.');
    }
}
