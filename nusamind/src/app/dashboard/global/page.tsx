'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import type { Product } from '@/types'
import { Sparkles, Copy, Check, Globe, Languages, Lightbulb, Clock, ArrowRight } from 'lucide-react'

interface TranslationResult {
  translated_name: string
  translated_description: string
  export_tips: string
  language: string
}

interface TranslationHistory {
  id: number
  product_name: string
  translated_name: string
  language: string
  created_at: string
}

export default function NusaGlobalPage() {
  const [products, setProducts] = useState<Product[]>([])
  const [selectedId, setSelectedId] = useState('')
  const [loading, setLoading] = useState(true)
  const [translating, setTranslating] = useState(false)
  const [language, setLanguage] = useState('english')
  const [result, setResult] = useState<TranslationResult | null>(null)
  const [history, setHistory] = useState<TranslationHistory[]>([])
  const [copiedFields, setCopiedFields] = useState<Record<string, boolean>>({})

  const selected = products.find((p) => p.id === Number(selectedId))

  useEffect(() => {
    Promise.all([
      fetch('/api/data/products').then((r) => r.json()),
      fetch('/api/data/translations?limit=10').then((r) => r.json()).catch(() => []),
    ]).then(([productsData, historyData]) => {
      if (Array.isArray(productsData)) setProducts(productsData)
      else if (productsData.data) setProducts(productsData.data)
      if (Array.isArray(historyData)) setHistory(historyData)
      else if (historyData.data) setHistory(historyData.data)
    }).finally(() => setLoading(false))
  }, [])

  const translate = async () => {
    if (!selected) return
    setTranslating(true)
    setResult(null)
    try {
      const res = await fetch('/api/ai/global', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_name: selected.name,
          description: selected.description,
          target_language: language,
        }),
      })
      const data = await res.json()
      const resData = data.data ?? data
      setResult({ ...resData, language })
      setHistory((prev) => [
        {
          id: Date.now(),
          product_name: selected.name,
          translated_name: resData.translated_name,
          language,
          created_at: new Date().toISOString(),
        },
        ...prev,
      ])
    } finally {
      setTranslating(false)
    }
  }

  const copyField = async (text: string, field: string) => {
    await navigator.clipboard.writeText(text)
    setCopiedFields((prev) => ({ ...prev, [field]: true }))
    setTimeout(() => setCopiedFields((prev) => ({ ...prev, [field]: false })), 2000)
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900 font-poppins">NusaGlobal</h1>
        <p className="text-sm text-gray-500 mt-0.5">Terjemahkan produk untuk pasar ekspor</p>
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
          <div className="mt-4 p-3 bg-gray-50 rounded-xl text-sm text-gray-600">
            <p className="font-medium text-gray-900">{selected.name}</p>
            {selected.description && <p className="mt-1 text-gray-500">{selected.description}</p>}
          </div>
        )}
      </Card>

      {products.length === 0 && (
        <EmptyState title="Belum ada produk" description="Tambahkan produk terlebih dahulu" />
      )}

      <Card>
        <CardHeader>
          <CardTitle>Bahasa Target</CardTitle>
        </CardHeader>
        <div className="flex gap-4">
          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="radio"
              name="language"
              value="english"
              checked={language === 'english'}
              onChange={(e) => setLanguage(e.target.value)}
              className="accent-[#0F9D8E]"
            />
            <span className="text-sm text-gray-700">English</span>
          </label>
          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="radio"
              name="language"
              value="mandarin"
              checked={language === 'mandarin'}
              onChange={(e) => setLanguage(e.target.value)}
              className="accent-[#0F9D8E]"
            />
            <span className="text-sm text-gray-700">中文 (Mandarin)</span>
          </label>
        </div>
      </Card>

      <Button className="w-full" disabled={!selected || translating} onClick={translate} loading={translating}>
        <Globe className="w-4 h-4" />
        Terjemahkan
      </Button>

      {result && (
        <>
          <Card className="border-[#0F9D8E]/20 bg-[#e8f5f3]/30">
            <CardHeader>
              <div className="flex items-center gap-2">
                <Languages className="w-4 h-4 text-[#0F9D8E]" />
                <CardTitle>Hasil Terjemahan ({result.language === 'english' ? 'English' : '中文'})</CardTitle>
              </div>
            </CardHeader>
            <div className="space-y-4">
              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs text-gray-500 font-medium uppercase">Nama Produk</span>
                  <button
                    onClick={() => copyField(result.translated_name, 'name')}
                    className="p-1 hover:bg-white rounded-lg transition-colors"
                  >
                    {copiedFields.name ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4 text-gray-400" />}
                  </button>
                </div>
                <p className="text-base font-semibold text-gray-900">{result.translated_name}</p>
              </div>
              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs text-gray-500 font-medium uppercase">Deskripsi</span>
                  <button
                    onClick={() => copyField(result.translated_description, 'desc')}
                    className="p-1 hover:bg-white rounded-lg transition-colors"
                  >
                    {copiedFields.desc ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4 text-gray-400" />}
                  </button>
                </div>
                <p className="text-sm text-gray-600 whitespace-pre-wrap">{result.translated_description}</p>
              </div>
            </div>
          </Card>

          <Card>
            <CardHeader>
              <div className="flex items-center gap-2">
                <Lightbulb className="w-4 h-4 text-[#F2B705]" />
                <CardTitle>Tips Ekspor</CardTitle>
              </div>
            </CardHeader>
            <p className="text-sm text-gray-600 whitespace-pre-wrap">{result.export_tips}</p>
          </Card>
        </>
      )}

      {history.length > 0 && (
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <Clock className="w-4 h-4 text-gray-400" />
              <CardTitle>Riwayat Terjemahan</CardTitle>
            </div>
          </CardHeader>
          <div className="space-y-3">
            {history.map((item) => (
              <div key={item.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl text-sm">
                <div className="min-w-0 flex-1">
                  <p className="font-medium text-gray-900 truncate">{item.product_name}</p>
                  <div className="flex items-center gap-1 text-gray-500 mt-0.5">
                    <span className="truncate">{item.translated_name}</span>
                    <Badge variant="info">{item.language === 'english' ? 'EN' : '中文'}</Badge>
                  </div>
                </div>
                <span className="text-xs text-gray-400 shrink-0 ml-2">{formatDate(item.created_at)}</span>
              </div>
            ))}
          </div>
        </Card>
      )}
    </div>
  )
}
