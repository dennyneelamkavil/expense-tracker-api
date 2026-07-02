<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Get paginated expenses for the authenticated user.
     *
     * Supports pagination using:
     *
     * - page
     * - per_page
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        $perPage = (int) $request->query('per_page', 10);

        // Prevent someone requesting 100000 records
        $perPage = max(1, min($perPage, 100));

        $expenses = $request->user()
            ->expenses()
            ->with('category')
            ->latest('expense_date')
            ->paginate($perPage)
            ->withQueryString();

        return ExpenseResource::collection($expenses);
    }

    /**
     * Create a new expense.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $this->authorize('create', Expense::class);

        $expense = $request->user()->expenses()->create(
            $request->validated()
        );

        $expense->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully.',
            'data' => new ExpenseResource($expense),
        ], 201);
    }

    /**
     * Retrieve an expense by ID.
     */
    public function show(Expense $expense): ExpenseResource
    {
        $this->authorize('view', $expense);

        $expense->load('category');

        return new ExpenseResource($expense);
    }

    /**
     * Update an existing expense.
     */
    public function update(
        UpdateExpenseRequest $request,
        Expense $expense
    ): JsonResponse {
        $this->authorize('update', $expense);

        $expense->update(
            $request->validated()
        );

        $expense->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully.',
            'data' => new ExpenseResource($expense),
        ]);
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.',
        ]);
    }
}
