<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowanceType;
use Illuminate\Http\Request;

class AllowanceTypeController extends Controller
{
    public function index()
    {
        $types = AllowanceType::orderBy('name')->paginate(20);

        return view('admin.payroll.settings.allowance-types', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:allowance_types,code',
            'amount' => 'required|numeric|min:0',
            'is_taxable' => 'nullable|boolean',
            'is_recurring' => 'nullable|boolean',
        ]);

        AllowanceType::create($validated);

        return redirect()->route('admin.payroll.settings.allowance-types')
            ->with('success', 'Allowance type created.');
    }

    public function update(Request $request, AllowanceType $allowanceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:allowance_types,code,'.$allowanceType->id,
            'amount' => 'required|numeric|min:0',
            'is_taxable' => 'nullable|boolean',
            'is_recurring' => 'nullable|boolean',
        ]);

        $allowanceType->update($validated);

        return redirect()->route('admin.payroll.settings.allowance-types')
            ->with('success', 'Allowance type updated.');
    }

    public function destroy(AllowanceType $allowanceType)
    {
        $allowanceType->delete();

        return redirect()->route('admin.payroll.settings.allowance-types')
            ->with('success', 'Allowance type deleted.');
    }
}
