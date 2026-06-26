<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HealthScore;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScoreWebController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return view('user.score.index', ['score' => null]);
        }

        $latestScore = HealthScore::where('business_id', $business->id)->latest('scored_at')->first();

        if ($latestScore && $latestScore->scored_at && $latestScore->scored_at->diffInHours(now()) < 24) {
            $score = $latestScore;
        } else {
            $transactions = Transaction::where('user_id', $user->id)
                ->where('transaction_date', '>=', now()->subDays(30))
                ->get();

            $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
            $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
            $transactionCount = $transactions->count();

            $ratio = $totalExpense > 0 ? $totalIncome / $totalExpense : ($totalIncome > 0 ? 1.5 : 0);

            if ($ratio >= 1.5) {
                $financialScore = 90;
            } elseif ($ratio >= 1.0) {
                $financialScore = 70;
            } elseif ($ratio >= 0.5) {
                $financialScore = 40;
            } else {
                $financialScore = 20;
            }

            $contentCount = $user->contentGenerations()->count();

            if ($contentCount >= 10) {
                $marketingScore = 80;
            } elseif ($contentCount >= 5) {
                $marketingScore = 60;
            } elseif ($contentCount >= 1) {
                $marketingScore = 40;
            } else {
                $marketingScore = 10;
            }

            if ($transactionCount >= 20) {
                $salesScore = 90;
            } elseif ($transactionCount >= 10) {
                $salesScore = 70;
            } elseif ($transactionCount >= 5) {
                $salesScore = 50;
            } else {
                $salesScore = 30;
            }

            $customerCount = $business->customers()->count();

            if ($customerCount >= 10) {
                $customerScore = 80;
            } elseif ($customerCount >= 5) {
                $customerScore = 60;
            } elseif ($customerCount >= 1) {
                $customerScore = 40;
            } else {
                $customerScore = 20;
            }

            $products = $business->products;
            $totalProducts = $products->count();
            $productsWithStock = $products->where('stock', '>', 0)->count();
            $stockPercentage = $totalProducts > 0 ? ($productsWithStock / $totalProducts) * 100 : 0;

            if ($stockPercentage >= 80) {
                $stockScore = 90;
            } elseif ($stockPercentage >= 50) {
                $stockScore = 70;
            } elseif ($stockPercentage >= 30) {
                $stockScore = 50;
            } else {
                $stockScore = 20;
            }

            $totalScore = (int) round(($financialScore + $marketingScore + $salesScore + $customerScore + $stockScore) / 5);

            $breakdownText = "Skor kesehatan bisnismu {$totalScore}/100. ";
            $breakdownText .= "Keuangan: {$financialScore}/100 (rasio pemasukan/pengeluaran " . number_format($ratio, 2) . "). ";
            $breakdownText .= "Marketing: {$marketingScore}/100 ({$contentCount} konten dibuat). ";
            $breakdownText .= "Penjualan: {$salesScore}/100 ({$transactionCount} transaksi 30 hari terakhir). ";
            $breakdownText .= "Pelanggan: {$customerScore}/100 ({$customerCount} pelanggan). ";
            $breakdownText .= "Stok: {$stockScore}/100 (" . number_format($stockPercentage, 0) . "% produk tersedia).";

            $recommendations = [];

            if ($financialScore < 70) {
                $recommendations[] = 'Tingkatkan rasio pemasukan dengan mengurangi pengeluaran yang tidak perlu atau menambah sumber pendapatan.';
            }
            if ($marketingScore < 60) {
                $recommendations[] = 'Gunakan fitur pembuatan konten AI Nusamind untuk memasarkan produk secara rutin.';
            }
            if ($salesScore < 70) {
                $recommendations[] = 'Tingkatkan frekuensi transaksi dengan Promo atau program loyalitas pelanggan.';
            }
            if ($customerScore < 60) {
                $recommendations[] = 'Kumpulkan data pelanggan untuk membangun hubungan dan meningkatkan penjualan berulang.';
            }
            if ($stockScore < 70) {
                $recommendations[] = 'Pastikan stok produk selalu tersedia dengan manajemen inventaris yang baik.';
            }

            if (empty($recommendations)) {
                $recommendations[] = 'Pertahankan kinerja bisnismu yang sudah baik! Terus tingkatkan untuk hasil yang lebih maksimal.';
            }

            $score = HealthScore::create([
                'business_id' => $business->id,
                'total_score' => $totalScore,
                'financial_score' => $financialScore,
                'marketing_score' => $marketingScore,
                'sales_score' => $salesScore,
                'customer_score' => $customerScore,
                'stock_score' => $stockScore,
                'breakdown_text' => $breakdownText,
                'recommendations' => $recommendations,
                'scored_at' => now(),
            ]);
        }

        return view('user.score.index', compact('score'));
    }

    public function history(): View
    {
        $business = Auth::user()->business;

        $scores = collect();
        if ($business) {
            $scores = HealthScore::where('business_id', $business->id)
                ->latest('scored_at')
                ->take(10)
                ->get();
        }

        return view('user.score.history', compact('scores'));
    }
}
