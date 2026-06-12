<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollGrade;
use Illuminate\Http\Request;

class PayrollGradeController extends Controller
{
    public function index()
    {
        $grades = PayrollGrade::latest()->paginate(20);

        return view('admin.payroll.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('admin.payroll.grades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_basic_salary' => 'required|numeric|min:0',
            'working_days_divisor' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        PayrollGrade::create($validated);

        return redirect()->route('admin.payroll.grades.index')
            ->with('success', 'Payroll grade created successfully.');
    }

    public function show(PayrollGrade $grade)
    {
        return view('admin.payroll.grades.show', compact('grade'));
    }

    public function edit(PayrollGrade $grade)
    {
        return view('admin.payroll.grades.edit', compact('grade'));
    }

    public function update(Request $request, PayrollGrade $grade)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_basic_salary' => 'required|numeric|min:0',
            'working_days_divisor' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $grade->update($validated);

        return redirect()->route('admin.payroll.grades.index')
            ->with('success', 'Payroll grade updated successfully.');
    }

    public function destroy(PayrollGrade $grade)
    {
        $grade->delete();

        return redirect()->route('admin.payroll.grades.index')
            ->with('success', 'Payroll grade deleted successfully.');
    }
}
