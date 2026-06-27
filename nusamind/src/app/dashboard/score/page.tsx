'use client'

import { useState, useEffect, useMemo } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { cn, getScoreColor, getScoreBg, formatDate } from '@/lib/utils'
import type { HealthScore } from '@/types'
import Link from 'next/link'
import {
  RefreshCw, Wallet, Image, TrendingUp, Users, Package,
  Lightbulb, ChevronRight, BarChart3, FileText
} from 'lucide-react'

const scoreMeta = [
  { key: 'financial_score', label: 'Financial', icon: Wallet },
  { key: 'marketing_score', label: 'Marketing', icon: Image },
  { key: 'sales_score', label: 'Sales', icon: TrendingUp },
  { key: 'customer_score', label: 'Customer', icon: Users },
  { key: 'stock_score', label: 'Stock', icon: Package },
] as const

export default function NusaScorePage() {
  const [score, setScore] = useState<HealthScore | null>(null)
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const fetchScore = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/health-scores/latest')
      const data = await res.json()
      setScore(data.data ?? data)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchScore() }, [])

  const refreshScore = async () => {
    setRefreshing(true)
    try {
      const res = await fetch('/api/data/health-scores/calculate', { method: 'POST' })
      const data = await res.json()
      setScore(data.data ?? data)
    } finally {
      setRefreshing(false)
    }
  }

  const totalScore = score?.total_score ?? 0
  const circumference = 2 * Math.PI * 54
  const strokeDashoffset = circumference - (totalScore / 100) * circumference
  const scoreColor = getScoreColor(totalScore).replace('text-', '')

  const ringColor = useMemo(() => {
    if (totalScore >= 80) return 'stroke-green-500'
    if (totalScore >= 60) return 'stroke-yellow-500'
    if (totalScore >= 40) return 'stroke-orange-500'
    return 'stroke-red-500'
  }, [totalScore])

  const recommendations = useMemo(() => {
    if (!score?.recommendations) return []
    try {
      const parsed = JSON.parse(score.recommendations)
      return Array.isArray(parsed) ? parsed : [String(score.recommendations)]
    } catch {
      return score.recommendations.split('\n').filter(Boolean)
    }
  }, [score?.recommendations])

  if (loading && !score) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-gray-900 font-poppins">NusaScore</h1>
          <p className="text-sm text-gray-500 mt-0.5">Skor kesehatan bisnis Anda</p>
        </div>
        <Button size="sm" variant="outline" onClick={refreshScore} loading={refreshing}>
          <RefreshCw className="w-4 h-4" />
          Perbarui
        </Button>
      </div>

      {!score && <EmptyState title="Belum ada skor" description="Klik Perbarui Skor untuk memulai" />}

      {score && (
        <>
          <Card className="flex flex-col items-center py-6">
            <div className="relative w-32 h-32">
              <svg className="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" strokeWidth="8" />
                <circle
                  cx="60" cy="60" r="54"
                  fill="none"
                  className={ringColor}
                  strokeWidth="8"
                  strokeLinecap="round"
                  strokeDasharray={circumference}
                  strokeDashoffset={strokeDashoffset}
                  style={{ transition: 'stroke-dashoffset 0.6s ease' }}
                />
              </svg>
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="text-center">
                  <p className={cn('text-3xl font-bold', getScoreColor(totalScore))}>{totalScore}</p>
                  <p className="text-[10px] text-gray-400 uppercase tracking-wider">/ 100</p>
                </div>
              </div>
            </div>
            {score.scored_at && (
              <p className="text-xs text-gray-400 mt-3">Terakhir diperbarui: {formatDate(score.scored_at)}</p>
            )}
          </Card>

          <div className="grid grid-cols-2 gap-3">
            {scoreMeta.map(({ key, label, icon: Icon }) => {
              const val = score[key as keyof HealthScore] as number | null ?? 0
              return (
                <Card key={key} className={cn('flex items-center gap-3 py-4', getScoreBg(val))}>
                  <div className="w-10 h-10 rounded-xl bg-white/80 flex items-center justify-center shrink-0">
                    <Icon className={cn('w-5 h-5', getScoreColor(val))} />
                  </div>
                  <div className="min-w-0">
                    <p className="text-xs text-gray-500">{label}</p>
                    <p className={cn('text-lg font-bold', getScoreColor(val))}>{val}</p>
                  </div>
                </Card>
              )
            })}
          </div>

          {score.breakdown_text && (
            <Card>
              <CardHeader>
                <div className="flex items-center gap-2">
                  <FileText className="w-4 h-4 text-[#0F9D8E]" />
                  <CardTitle>Rincian Skor</CardTitle>
                </div>
              </CardHeader>
              <p className="text-sm text-gray-600 whitespace-pre-wrap">{score.breakdown_text}</p>
            </Card>
          )}

          {recommendations.length > 0 && (
            <Card>
              <CardHeader>
                <div className="flex items-center gap-2">
                  <Lightbulb className="w-4 h-4 text-[#F2B705]" />
                  <CardTitle>Rekomendasi</CardTitle>
                </div>
              </CardHeader>
              <div className="space-y-3">
                {recommendations.map((rec, i) => (
                  <div key={i} className="flex items-start gap-3">
                    <Lightbulb className="w-4 h-4 text-[#F2B705] shrink-0 mt-0.5" />
                    <p className="text-sm text-gray-600">{typeof rec === 'string' ? rec : String(rec)}</p>
                  </div>
                ))}
              </div>
            </Card>
          )}

          <Link
            href="/score/history"
            className="btn-outline w-full inline-flex items-center justify-center gap-2"
          >
            <BarChart3 className="w-4 h-4" />
            Lihat Riwayat Skor
            <ChevronRight className="w-4 h-4" />
          </Link>
        </>
      )}
    </div>
  )
}
