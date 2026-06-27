'use client'

import { useEffect, useState } from 'react'
import { formatCurrency, formatDate } from '@/lib/utils'
import { Button } from '@/components/ui/Button'
import { Card, CardTitle } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { EmptyState } from '@/components/ui/EmptyState'
import { BarChart3, Lightbulb, TrendingUp, RefreshCw, Sparkles, Wallet, Package, ClipboardList } from 'lucide-react'

interface InsightResult {
  period_start: string
  period_end: string
  narrative_text: string
  summary_data: {
    total_revenue: number
    total_expense: number
    top_product: string
    total_transactions: number
  }
  suggestions: string[]
}

interface Briefing {
  id: number
  period_start: string
  period_end: string
  narrative_text: string
  created_at: string
}

export default function InsightPage() {
  const [loading, setLoading] = useState(false)
  const [result, setResult] = useState<InsightResult | null>(null)
  const [history, setHistory] = useState<Briefing[]>([])
  const [error, setError] = useState('')

  useEffect(() => {
    async function fetchHistory() {
      const res = await fetch('/api/data/insights')
      if (res.ok) {
        const data = await res.json()
        setHistory(data.data ?? data ?? [])
      }
    }
    fetchHistory()
  }, [])

  async function handleGenerate() {
    setLoading(true)
    setError('')
    setResult(null)
    try {
      const res = await fetch('/api/ai/insight', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.error || 'Gagal membuat briefing')
      setResult(data)
    } catch (e: any) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }

  function formatPeriod(start: string, end: string) {
    const s = new Date(start)
    const e = new Date(end)
    return `${s.getDate()} ${s.toLocaleDateString('id-ID', { month: 'short' })} - ${e.getDate()} ${e.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' })}`
  }

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900">NusaInsight</h1>
        <p className="text-sm text-gray-500">Briefing bisnis mingguan berbasis AI</p>
      </div>

      {!result && !loading && (
        <Card>
          <div className="flex flex-col items-center text-center py-6">
            <BarChart3 className="w-12 h-12 text-[#0F9D8E] mb-3" />
            <CardTitle>Briefing Bisnis Anda</CardTitle>
            <p className="text-sm text-gray-500 mt-2 max-w-xs">
              Dapatkan ringkasan performa bisnis, rekomendasi, dan insight mingguan yang dipersonalisasi oleh AI.
            </p>
            <Button onClick={handleGenerate} className="mt-5" size="lg">
              <Sparkles className="w-5 h-5" />
              Buat Briefing
            </Button>
          </div>
        </Card>
      )}

      {error && !loading && (
        <Card className="border-red-200 bg-red-50">
          <p className="text-sm text-red-600">{error}</p>
        </Card>
      )}

      {loading && (
        <Card className="flex flex-col items-center justify-center gap-4 py-12">
          <LoadingSpinner className="w-10 h-10" />
          <div className="text-center">
            <p className="text-sm font-medium text-gray-700">AI sedang menganalisis bisnis Anda...</p>
            <p className="text-xs text-gray-400 mt-1">Memproses data transaksi terbaru</p>
          </div>
        </Card>
      )}

      {result && !loading && (
        <Card>
          <div className="flex items-center gap-2 mb-1">
            <BarChart3 className="w-5 h-5 text-[#0F9D8E]" />
            <CardTitle>Briefing Bisnis</CardTitle>
          </div>
          <p className="text-sm text-[#0F9D8E] font-medium mb-4">
            {formatPeriod(result.period_start, result.period_end)}
          </p>

          <div className="bg-[#e8f5f3]/40 rounded-xl p-4 mb-4">
            <p className="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">
              {result.narrative_text}
            </p>
          </div>

          <div className="grid grid-cols-2 gap-3 mb-4">
            <div className="bg-green-50 rounded-xl p-3">
              <div className="flex items-center gap-1.5 mb-1">
                <TrendingUp className="w-4 h-4 text-green-600" />
                <span className="text-xs text-gray-500">Revenue</span>
              </div>
              <p className="text-sm font-bold text-green-700">{formatCurrency(result.summary_data.total_revenue)}</p>
            </div>
            <div className="bg-red-50 rounded-xl p-3">
              <div className="flex items-center gap-1.5 mb-1">
                <Wallet className="w-4 h-4 text-red-600" />
                <span className="text-xs text-gray-500">Expense</span>
              </div>
              <p className="text-sm font-bold text-red-700">{formatCurrency(result.summary_data.total_expense)}</p>
            </div>
            <div className="bg-blue-50 rounded-xl p-3">
              <div className="flex items-center gap-1.5 mb-1">
                <Package className="w-4 h-4 text-blue-600" />
                <span className="text-xs text-gray-500">Top Product</span>
              </div>
              <p className="text-sm font-bold text-blue-700 truncate">{result.summary_data.top_product}</p>
            </div>
            <div className="bg-purple-50 rounded-xl p-3">
              <div className="flex items-center gap-1.5 mb-1">
                <ClipboardList className="w-4 h-4 text-purple-600" />
                <span className="text-xs text-gray-500">Transaksi</span>
              </div>
              <p className="text-sm font-bold text-purple-700">{result.summary_data.total_transactions}</p>
            </div>
          </div>

          {result.suggestions && result.suggestions.length > 0 && (
            <div>
              <div className="flex items-center gap-1.5 mb-2">
                <Lightbulb className="w-4 h-4 text-[#F2B705]" />
                <span className="text-sm font-semibold text-gray-700">Saran untuk Anda</span>
              </div>
              <ul className="space-y-2">
                {result.suggestions.map((s, i) => (
                  <li key={i} className="flex items-start gap-2 text-sm text-gray-600">
                    <Lightbulb className="w-4 h-4 text-[#F2B705] mt-0.5 shrink-0" />
                    <span>{s}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          <div className="mt-4">
            <Button variant="outline" onClick={handleGenerate} loading={loading}>
              <RefreshCw className="w-4 h-4" />
              Refresh Briefing
            </Button>
          </div>
        </Card>
      )}

      <div>
        <h2 className="font-semibold text-gray-900 mb-3">Riwayat Briefing</h2>
        {history.length === 0 ? (
          <EmptyState
            icon={BarChart3}
            title="Belum ada briefing"
            description="Buat briefing pertama Anda"
          />
        ) : (
          <div className="space-y-2">
            {history.map(b => (
              <Card key={b.id} className="cursor-pointer hover:shadow-md transition-shadow">
                <div className="flex items-center gap-2 mb-1.5">
                  <BarChart3 className="w-4 h-4 text-[#0F9D8E]" />
                  <span className="text-xs text-[#0F9D8E] font-medium">
                    {b.period_start && b.period_end
                      ? formatPeriod(b.period_start, b.period_end)
                      : formatDate(b.created_at)}
                  </span>
                </div>
                <p className="text-sm text-gray-600 line-clamp-2">{b.narrative_text}</p>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
