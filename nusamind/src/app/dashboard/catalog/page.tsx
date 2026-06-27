'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { LoadingSpinner, PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import type { Product } from '@/types'
import { Sparkles, Copy, Check, Package, FileText, Hash, ShoppingCart } from 'lucide-react'

interface CatalogResult {
  optimized_name: string
  optimized_description: string
  keywords: string[]
}

export default function NusaCatalogPage() {
  const [products, setProducts] = useState<Product[]>([])
  const [selectedId, setSelectedId] = useState('')
  const [loading, setLoading] = useState(true)
  const [optimizing, setOptimizing] = useState(false)
  const [result, setResult] = useState<CatalogResult | null>(null)
  const [copiedFields, setCopiedFields] = useState<Record<string, boolean>>({})
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

  const optimize = async () => {
    if (!selected) return
    setOptimizing(true)
    setResult(null)
    try {
      const res = await fetch('/api/ai/catalog', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_name: selected.name,
          description: selected.description,
          tags: selected.tags,
        }),
      })
      const data = await res.json()
      setResult(data.data ?? data)
    } finally {
      setOptimizing(false)
    }
  }

  const copyField = async (text: string, field: string) => {
    await navigator.clipboard.writeText(text)
    setCopiedFields((prev) => ({ ...prev, [field]: true }))
    setTimeout(() => setCopiedFields((prev) => ({ ...prev, [field]: false })), 2000)
  }

  const applyChanges = async () => {
    if (!selected || !result) return
    setApplying(true)
    try {
      const res = await fetch(`/api/data/products`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: selected.id,
          name: result.optimized_name,
          description: result.optimized_description,
        }),
      })
      if (res.ok) {
        setProducts((prev) =>
          prev.map((p) =>
            p.id === selected.id
              ? { ...p, name: result.optimized_name, description: result.optimized_description }
              : p,
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
        <h1 className="text-xl font-bold text-gray-900 font-poppins">NusaCatalog</h1>
        <p className="text-sm text-gray-500 mt-0.5">Optimasi katalog produk dengan AI</p>
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
            <option key={p.id} value={p.id}>{p.name}</option>
          ))}
        </select>

        {selected && (
          <div className="mt-4 space-y-2 text-sm text-gray-600">
            <div className="flex items-start gap-2">
              <Package className="w-4 h-4 text-[#0F9D8E] mt-0.5 shrink-0" />
              <div>
                <p className="font-medium text-gray-900">{selected.name}</p>
                {selected.description && <p className="text-gray-500 mt-0.5">{selected.description}</p>}
              </div>
            </div>
          </div>
        )}
      </Card>

      {products.length === 0 && (
        <EmptyState title="Belum ada produk" description="Tambahkan produk terlebih dahulu" />
      )}

      <Button className="w-full" disabled={!selected || optimizing} onClick={optimize} loading={optimizing}>
        <Sparkles className="w-4 h-4" />
        Optimasi
      </Button>

      {result && (
        <>
          <Card className="border-[#0F9D8E]/20">
            <CardHeader>
              <div className="flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-[#0F9D8E]" />
                <CardTitle>Hasil Optimasi</CardTitle>
              </div>
            </CardHeader>
            <div className="space-y-4">
              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs text-gray-500 font-medium uppercase">Nama Produk</span>
                  <button
                    onClick={() => copyField(result.optimized_name, 'name')}
                    className="p-1 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    {copiedFields.name ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4 text-gray-400" />}
                  </button>
                </div>
                <p className="text-lg font-semibold text-gray-900">{result.optimized_name}</p>
              </div>
              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs text-gray-500 font-medium uppercase">Deskripsi</span>
                  <button
                    onClick={() => copyField(result.optimized_description, 'desc')}
                    className="p-1 hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    {copiedFields.desc ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4 text-gray-400" />}
                  </button>
                </div>
                <p className="text-sm text-gray-600 whitespace-pre-wrap">{result.optimized_description}</p>
              </div>
              <div>
                <span className="text-xs text-gray-500 font-medium uppercase block mb-2">Kata Kunci</span>
                <div className="flex flex-wrap gap-2">
                  {result.keywords?.map((kw) => (
                    <Badge key={kw} variant="info">{kw}</Badge>
                  ))}
                </div>
              </div>
            </div>
          </Card>

          <Button className="w-full" variant="secondary" onClick={applyChanges} loading={applying}>
            <ShoppingCart className="w-4 h-4" />
            Terapkan
          </Button>
        </>
      )}
    </div>
  )
}
