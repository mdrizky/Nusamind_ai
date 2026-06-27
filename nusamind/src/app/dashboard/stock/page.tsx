'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { Modal } from '@/components/ui/Modal'
import { Package, Sparkles, Plus, History, TrendingUp, AlertTriangle, CheckCircle2 } from 'lucide-react'
import Link from 'next/link'
import type { Product } from '@/types'

interface StockRecommendation {
  product_name: string
  status: string
  current_stock: number
  recommended_restock: number
  reason: string
}

export default function StockPage() {
  const [products, setProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(true)
  const [aiLoading, setAiLoading] = useState(false)
  const [recommendations, setRecommendations] = useState<StockRecommendation[] | null>(null)
  const [modalOpen, setModalOpen] = useState(false)
  const [selectedProduct, setSelectedProduct] = useState<Product | null>(null)
  const [addQty, setAddQty] = useState('')
  const [addReason, setAddReason] = useState('')
  const [saving, setSaving] = useState(false)

  const fetchProducts = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/products')
      const json = await res.json()
      setProducts(json.data || [])
    } catch {
      setProducts([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchProducts() }, [])

  const getStockStatus = (stock: number, min: number) => {
    if (stock <= min) return { label: 'Kritis', variant: 'danger' as const }
    if (stock <= min * 2) return { label: 'Menipis', variant: 'warning' as const }
    return { label: 'Aman', variant: 'success' as const }
  }

  const analyzeStock = async () => {
    setAiLoading(true)
    setRecommendations(null)
    try {
      const res = await fetch('/api/ai/stock', { method: 'POST' })
      const json = await res.json()
      if (json.data) setRecommendations(json.data)
    } catch {
      // silent
    } finally {
      setAiLoading(false)
    }
  }

  const openAddModal = (product: Product) => {
    setSelectedProduct(product)
    setAddQty('')
    setAddReason('')
    setModalOpen(true)
  }

  const recordMovement = async () => {
    if (!selectedProduct || !addQty) return
    setSaving(true)
    try {
      await fetch('/api/data/stock-movements', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_id: selectedProduct.id,
          movement_type: 'in',
          quantity: parseInt(addQty),
          reason: addReason || 'Penambahan stok manual',
        }),
      })
      setModalOpen(false)
      fetchProducts()
    } catch {
      // silent
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="p-4 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">NusaStock</h1>
        <Link href="/stock/movements">
          <Button variant="outline" size="sm">
            <History className="w-4 h-4" />
            Riwayat
          </Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Daftar Produk</CardTitle>
        </CardHeader>
        {loading ? (
          <div className="flex justify-center py-8"><LoadingSpinner /></div>
        ) : products.length === 0 ? (
          <EmptyState icon={Package} title="Belum ada produk" description="Tambahkan produk untuk mulai memantau stok" />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="text-left text-gray-500 border-b border-gray-100">
                  <th className="pb-2 font-medium">Produk</th>
                  <th className="pb-2 font-medium">Stok</th>
                  <th className="pb-2 font-medium">Min. Alert</th>
                  <th className="pb-2 font-medium">Status</th>
                  <th className="pb-2 font-medium"></th>
                </tr>
              </thead>
              <tbody>
                {products.map((p) => {
                  const status = getStockStatus(p.stock, p.min_stock_alert)
                  return (
                    <tr key={p.id} className="border-b border-gray-50">
                      <td className="py-3 text-gray-900 font-medium">{p.name}</td>
                      <td className="py-3 text-gray-700">{p.stock} {p.unit}</td>
                      <td className="py-3 text-gray-500">{p.min_stock_alert}</td>
                      <td className="py-3">
                        <Badge variant={status.variant}>{status.label}</Badge>
                      </td>
                      <td className="py-3">
                        <button
                          onClick={() => openAddModal(p)}
                          className="p-1.5 hover:bg-[#0F9D8E]/10 rounded-lg text-gray-400 hover:text-[#0F9D8E] transition-colors"
                        >
                          <Plus className="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}
        <div className="mt-4">
          <Button onClick={analyzeStock} loading={aiLoading}>
            <Sparkles className="w-4 h-4" />
            Analisis Stok
          </Button>
        </div>
      </Card>

      {aiLoading && (
        <div className="flex justify-center py-4">
          <LoadingSpinner />
        </div>
      )}

      {recommendations && !aiLoading && (
        <Card>
          <CardHeader>
            <CardTitle>Rekomendasi AI</CardTitle>
          </CardHeader>
          <div className="space-y-3">
            {recommendations.map((rec, i) => (
              <div key={i} className="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                <div className="p-2 bg-[#0F9D8E]/10 rounded-lg">
                  <TrendingUp className="w-4 h-4 text-[#0F9D8E]" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="font-medium text-sm text-gray-900">{rec.product_name}</p>
                  <p className="text-xs text-gray-500 mt-0.5">
                    Stok: {rec.current_stock} → Rekomendasi restok: {rec.recommended_restock}
                  </p>
                  <p className="text-xs text-gray-400 mt-0.5">{rec.reason}</p>
                </div>
              </div>
            ))}
          </div>
        </Card>
      )}

      <Modal open={modalOpen} onClose={() => setModalOpen(false)} title={`Tambah Stok - ${selectedProduct?.name || ''}`}>
        <div className="space-y-4">
          <Input
            label="Jumlah"
            type="number"
            value={addQty}
            onChange={(e) => setAddQty(e.target.value)}
            placeholder="Masukkan jumlah"
          />
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Alasan (opsional)</label>
            <textarea
              value={addReason}
              onChange={(e) => setAddReason(e.target.value)}
              placeholder="Alasan penambahan stok"
              className="input-field min-h-[80px] resize-none"
            />
          </div>
          <Button className="w-full" onClick={recordMovement} loading={saving} disabled={!addQty}>
            <CheckCircle2 className="w-4 h-4" />
            Catat Stok Masuk
          </Button>
        </div>
      </Modal>
    </div>
  )
}
