<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
        ]);

        Expense::create([
            'description' => $validated['title'],
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
        ]);

        return back()->with('success', 'Dépense enregistrée avec succès.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $expense->update([
            'description' => $validated['title'],
            'amount' => $validated['amount'],
        ]);

        return back()->with('success', 'Dépense modifiée avec succès.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return back()->with('success', 'Dépense supprimée avec succès.');
    }
}
