'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { getScoreColor, getScoreBg, formatDateShort, formatDate, cn } from '@/lib/utils'
import type { HealthScore } from '@/types'
import { BarChart3, TrendingUp, TrendingDown, Minus, ChevronLeft } from 'lucide-react'
import Link from 'next/link'

export default function ScoreHistoryPage() {
  const [scores, setScores] = useState<HealthScore[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch('/api/data/health-scores')
      .then((r) => r.json())
      .then((data) => {
        if (Array.isArray(data)) setScores(data)
        else if (data.data) setScores(data.data)
      })
      .finally(() => setLoading(false))
  }, [])

  if (loading) return <PageLoading />

  const maxScore = 100

  return (
    <div className="p-4 space-y-5">
      <div className="flex items-center gap-3">
        <Link href="/score" className="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
          <ChevronLeft className="w-5 h-5 text-gray-500" />
        </Link>
        <div>
          <h1 className="text-xl font-bold text-gray-900 font-poppins">Riwayat Skor</h1>
          <p className="text-sm text-gray-500 mt-0.5">Perkembangan kesehatan bisnis</p>
        </div>
      </div>

      {scores.length === 0 && (
        <EmptyState title="Belum ada riwayat" description="Skor akan muncul setelah perhitungan pertama" />
      )}

      <div className="space-y-3">
        {scores.map((s, i) => {
          const prevScore = i < scores.length - 1 ? scores[i + 1].total_score : null
          const diff = prevScore !== null ? s.total_score - prevScore : null
          const scoreColor = getScoreColor(s.total_score)
          const subScores = [
            { label: 'Financial', value: s.financial_score },
            { label: 'Marketing', value: s.marketing_score },
            { label: 'Sales', value: s.sales_score },
            { label: 'Customer', value: s.customer_score },
            { label: 'Stock', value: s.stock_score },
          ]

          return (
            <Card key={s.id}>
              <div className="flex items-center justify-between mb-3">
                <div>
                  <p className="text-sm font-medium text-gray-900">{formatDate(s.scored_at)}</p>
                  <div className="flex items-center gap-2 mt-0.5">
                    <span className={cn('text-2xl font-bold', scoreColor)}>{s.total_score}</span>
                    {diff !== null && (
                      <span className={cn(
                        'flex items-center gap-0.5 text-xs font-medium',
                        diff > 0 ? 'text-green-600' : diff < 0 ? 'text-red-600' : 'text-gray-400',
                      )}>
                        {diff > 0 ? <TrendingUp className="w-3 h-3" /> : diff < 0 ? <TrendingDown className="w-3 h-3" /> : <Minus className="w-3 h-3" />}
                        {Math.abs(diff)}
                      </span>
                    )}
                  </div>
                </div>
                <div className={cn('w-12 h-12 rounded-full flex items-center justify-center', getScoreBg(s.total_score))}>
                  <span className={cn('text-sm font-bold', scoreColor)}>{s.total_score}</span>
                </div>
              </div>

              <div className="space-y-2">
                {subScores.map((sub) => {
                  const val = sub.value ?? 0
                  const pct = (val / maxScore) * 100
                  return (
                    <div key={sub.label} className="flex items-center gap-2 text-xs">
                      <span className="w-16 text-gray-500 shrink-0">{sub.label}</span>
                      <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div
                          className={cn('h-full rounded-full transition-all', getScoreBg(val))}
                          style={{ width: `${pct}%` }}
                        />
                      </div>
                      <span className={cn('w-6 text-right font-medium', getScoreColor(val))}>
                        {val}
                      </span>
                    </div>
                  )
                })}
              </div>
            </Card>
          )
        })}
      </div>
    </div>
  )
}
