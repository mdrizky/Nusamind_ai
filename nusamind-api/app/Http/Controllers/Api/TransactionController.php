<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::where('user_id', $request->user()->id);

        if ($request->filter === 'today') {
            $query->whereDate('transaction_date', today());
        } elseif ($request->filter === 'week') {
            $query->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->filter === 'month') {
            $query->whereMonth('transaction_date', now()->month)
                  ->whereYear('transaction_date', now()->year);
        }

        if ($request->type && in_array($request->type, ['pemasukan', 'pengeluaran'])) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_income' => (int) $transactions->where('type', 'pemasukan')->sum('amount'),
            'total_expense' => (int) $transactions->where('type', 'pengeluaran')->sum('amount'),
            'balance' => (int) ($transactions->where('type', 'pemasukan')->sum('amount') - $transactions->where('type', 'pengeluaran')->sum('amount')),
        ];

        return response()->json([
            'transactions' => $transactions,
            'summary' => $summary,
        ]);
    }

    public function storeBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transactions' => 'required|array|min:1',
            'transactions.*.type' => 'required|in:pemasukan,pengeluaran',
            'transactions.*.item_name' => 'required|string|max:150',
            'transactions.*.quantity' => 'nullable|integer|min:1',
            'transactions.*.amount' => 'required|integer|min:0',
            'transactions.*.product_id' => 'nullable|exists:products,id',
            'transactions.*.source' => 'required|in:ai_text,ai_voice,manual',
            'transactions.*.raw_input' => 'nullable|string',
        ]);

        $created = [];
        foreach ($validated['transactions'] as $tx) {
            $created[] = Transaction::create([
                'user_id' => $request->user()->id,
                'product_id' => $tx['product_id'] ?? null,
                'type' => $tx['type'],
                'item_name' => $tx['item_name'],
                'quantity' => $tx['quantity'] ?? null,
                'amount' => $tx['amount'],
                'source' => $tx['source'],
                'raw_input' => $tx['raw_input'] ?? null,
                'transaction_date' => today(),
            ]);
        }

        return response()->json([
            'message' => 'Transaksi tersimpan',
            'count' => count($created),
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json(['transaction' => $transaction]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|in:pemasukan,pengeluaran',
            'item_name' => 'sometimes|string|max:150',
            'quantity' => 'nullable|integer|min:1',
            'amount' => 'sometimes|integer|min:0',
            'transaction_date' => 'sometimes|date',
        ]);

        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'transaction' => $transaction->fresh(),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);
        $transaction->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}
