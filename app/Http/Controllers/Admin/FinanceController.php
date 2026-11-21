<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /** Hitung pemasukan, pengeluaran, dan saldo bersih untuk rentang tanggal terpilih. */
    public function index(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $data['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $data['end_date'] ?? now()->toDateString();
        $period = [$startDate.' 00:00:00', $endDate.' 23:59:59'];

        $incomeQuery = Order::with('customer')
            ->where('payment_status', 'lunas')
            ->whereBetween('paid_at', $period);
        $expenseQuery = Expense::query()
            ->whereDate('spent_on', '>=', $startDate)
            ->whereDate('spent_on', '<=', $endDate);

        $income = (clone $incomeQuery)->sum('total');
        $expenses = (clone $expenseQuery)->sum('amount');
        $incomeOrders = $incomeQuery->latest('paid_at')->paginate(10, ['*'], 'income_page')->withQueryString();
        $expenseEntries = $expenseQuery->latest('spent_on')->latest('id')->paginate(10, ['*'], 'expense_page')->withQueryString();

        return view('admin.finance.index', [
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
            'incomeOrders' => $incomeOrders,
            'expenseEntries' => $expenseEntries,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /** Validasi dan catat satu pengeluaran operasional oleh admin yang sedang masuk. */
    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'spent_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->expenses()->create($data);

        return redirect()->route('admin.finance.index', [
            'start_date' => substr($data['spent_on'], 0, 10),
            'end_date' => substr($data['spent_on'], 0, 10),
        ])->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /** Hapus catatan pengeluaran yang dipilih dari laporan keuangan. */
    public function destroyExpense(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
