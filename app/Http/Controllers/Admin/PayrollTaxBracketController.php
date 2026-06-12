<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollTaxBracket;
use Illuminate\Http\Request;

class PayrollTaxBracketController extends Controller
{
    public function index()
    {
        $brackets = PayrollTaxBracket::orderBy('from_amount')->paginate(20);

        return view('admin.payroll.settings.tax-brackets', compact('brackets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_amount' => 'required|numeric|min:0',
            'to_amount' => 'nullable|numeric|gt:from_amount',
            'rate' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        PayrollTaxBracket::create($validated);

        return redirect()->route('admin.payroll.settings.tax-brackets')
            ->with('success', 'Tax bracket created.');
    }

    public function update(Request $request, PayrollTaxBracket $taxBracket)
    {
        $validated = $request->validate([
            'from_amount' => 'required|numeric|min:0',
            'to_amount' => 'nullable|numeric|gt:from_amount',
            'rate' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        $taxBracket->update($validated);

        return redirect()->route('admin.payroll.settings.tax-brackets')
            ->with('success', 'Tax bracket updated.');
    }

    public function destroy(PayrollTaxBracket $taxBracket)
    {
        $taxBracket->delete();

        return redirect()->route('admin.payroll.settings.tax-brackets')
            ->with('success', 'Tax bracket deleted.');
    }
}
