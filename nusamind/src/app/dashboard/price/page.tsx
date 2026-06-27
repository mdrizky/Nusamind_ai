'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { LoadingSpinner, PageLoading } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatCurrency, cn } from '@/lib/utils'
import type { Product } from '@/types'
import { Sparkles, ArrowRightLeft, AlertCircle, TrendingUp, DollarSign, Package, ArrowRight, FlaskConical } from 'lucide-react'
import Link from 'next/link'

interface PriceResult {
  recommended_price: number
  min_price: number
  max_price: number
  reasoning: string
  market_insight?: string
}

export default function NusaPricePage() {
  const [products, setProducts] = useState<Product[]>([])
  const [selectedId, setSelectedId] = useState('')
  const [loading, setLoading] = useState(true)
  const [analyzing, setAnalyzing] = useState(false)
  const [result, setResult] = useState<PriceResult | null>(null)
  const [applying, setApplying] = useState(false)

  const selected = products.find((p) => p.id === Number(selectedId))

  useEffect(() => {
    fetch('/api/data/products')
      .then((r) => r.json())
      .then((data) => {
        if (Array.isArray(data)) setProducts(data)
        else if (data.data) setProducts(data.data)
      })
      .finally(() => setLoading(false))
  }, [])

  const analyzePrice = async () => {
    if (!selected) return
    setAnalyzing(true)
    setResult(null)
    try {
      const res = await fetch('/api/ai/price', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_name: selected.name,
          current_price: selected.price,
          cost_estimate: selected.cost_estimate,
          stock: selected.stock,
        }),
      })
      const data = await res.json()
      setResult(data.data ?? data)
    } finally {
      setAnalyzing(false)
    }
  }

  const applyRecommendation = async () => {
    if (!selected || !result) return
    setApplying(true)
    try {
      const res = await fetch(`/api/data/products`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: selected.id, price: result.recommended_price }),
      })
      if (res.ok) {
        setProducts((prev) =>
          prev.map((p) =>
            p.id === selected.id ? { ...p, price: result.recommended_price } : p,
          ),
        )
      }
    } finally {
      setApplying(false)
    }
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900 font-poppins">NusaPrice</h1>
        <p className="text-sm text-gray-500 mt-0.5">Rekomendasi harga berbasis AI</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Pilih Produk</CardTitle>
        </CardHeader>
        <select
          className="input-field"
          value={selectedId}
          onChange={(e) => { setSelectedId(e.target.value); setResult(null) }}
        >
          <option value="">-- Pilih Produk --</option>
          {products.map((p) => (
            <option key={p.id} value={p.id}>
              {p.name}
            </option>
          ))}
        </select>

        {selected && (
          <div className="mt-4 space-y-2 text-sm">
            <div className="flex items-center gap-2 text-gray-600">
              <DollarSign className="w-4 h-4 text-[#0F9D8E]" />
              <span>Harga Saat Ini: <strong className="text-gray-900">{formatCurrency(selected.price)}</strong></span>
            </div>
            {selected.cost_estimate != null && (
              <div className="flex items-center gap-2 text-gray-600">
                <TrendingUp className="w-4 h-4 text-[#F2B705]" />
                <span>Estimasi Biaya: <strong className="text-gray-900">{formatCurrency(selected.cost_estimate)}</strong></span>
              </div>
            )}
            <div className="flex items-center gap-2 text-gray-600">
              <Package className="w-4 h-4 text-blue-500" />
              <span>Stok: <strong className="text-gray-900">{selected.stock}</strong></span>
            </div>
          </div>
        )}
      </Card>

      {products.length === 0 && (
        <EmptyState title="Belum ada produk" description="Tambahkan produk terlebih dahulu" />
      )}

      <Button className="w-full" disabled={!selected || analyzing} onClick={analyzePrice} loading={analyzing}>
        <Sparkles className="w-4 h-4" />
        Analisis Harga
      </Button>

      {result && (
        <>
          <Card className="border-[#0F9D8E]/20 bg-[#e8f5f3]/30">
            <div className="text-center py-2">
              <p className="text-xs text-gray-500 uppercase tracking-wider">Harga Rekomendasi</p>
              <p className="text-3xl font-bold text-[#0F9D8E] mt-1">{formatCurrency(result.recommended_price)}</p>
              <p className="text-sm text-gray-500 mt-1">
                Rentang: {formatCurrency(result.min_price)} – {formatCurrency(result.max_price)}
              </p>
            </div>
            <div className="mt-4 flex items-center justify-center gap-2 text-sm">
              <span className="text-gray-500">{formatCurrency(selected?.price ?? 0)}</span>
              <ArrowRightLeft className="w-4 h-4 text-gray-400" />
              <span className="text-[#0F9D8E] font-semibold">{formatCurrency(result.recommended_price)}</span>
            </div>
          </Card>

          <AiResultCard title="Alasan & Analisis">
            {result.reasoning}
          </AiResultCard>

          {result.market_insight && (
            <AiResultCard title="Market Insight">
              {result.market_insight}
            </AiResultCard>
          )}

          <div className="flex gap-3">
            <Button className="flex-1" onClick={applyRecommendation} loading={applying}>
              <TrendingUp className="w-4 h-4" />
              Terapkan Rekomendasi
            </Button>
            <Link href="/price/hpp" className="btn-outline inline-flex items-center justify-center gap-2 flex-1">
              <FlaskConical className="w-4 h-4" />
              Hitung HPP
            </Link>
          </div>
        </>
      )}
    </div>
  )
}
