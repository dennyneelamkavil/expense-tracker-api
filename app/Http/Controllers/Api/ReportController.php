<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportFilterRequest;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Expense summary grouped by category.
     */
    public function expenseSummary(ReportFilterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Expense::query()
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $request->user()->id);

        if (!empty($validated['from'])) {
            $query->whereDate('expense_date', '>=', $validated['from']);
        }

        if (!empty($validated['to'])) {
            $query->whereDate('expense_date', '<=', $validated['to']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('expenses.category_id', $validated['category_id']);
        }

        $report = $query
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(expenses.amount) as total_amount')
            )
            ->orderByDesc('total_amount')
            ->get();

        return response()->json([
            'success' => true,
            'filters' => $validated,
            'data' => $report,
        ]);
    }

    /**
     * Average daily spending.
     */
    public function averageDailySpending(
        ReportFilterRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $query = Expense::query()
            ->where('user_id', $request->user()->id);

        if (!empty($validated['from'])) {
            $query->whereDate('expense_date', '>=', $validated['from']);
        }

        if (!empty($validated['to'])) {
            $query->whereDate('expense_date', '<=', $validated['to']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        $totalExpense = (float) $query->sum('amount');

        if (!empty($validated['from']) && !empty($validated['to'])) {

            $days = Carbon::parse($validated['from'])
                ->diffInDays(Carbon::parse($validated['to'])) + 1;
        } else {

            $firstExpense = Expense::where('user_id', $request->user()->id)
                ->oldest('expense_date')
                ->first();

            if ($firstExpense) {
                $days = Carbon::parse($firstExpense->expense_date)
                    ->diffInDays(now()) + 1;
            } else {
                $days = 1;
            }
        }

        $average = round($totalExpense / max($days, 1), 2);

        return response()->json([
            'success' => true,
            'filters' => $validated,
            'data' => [
                'total_expense' => $totalExpense,
                'number_of_days' => $days,
                'average_daily_spending' => $average,
            ],
        ]);
    }
}
