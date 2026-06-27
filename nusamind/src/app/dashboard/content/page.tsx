'use client'

import { useEffect, useState } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardTitle } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { EmptyState } from '@/components/ui/EmptyState'
import { Image, Sparkles, BookOpen, Lightbulb, RefreshCw, Save } from 'lucide-react'

interface Product {
  id: number
  name: string
}

interface ContentResult {
  caption_text: string
  hashtags: string
  cta: string
  platform_tips: string
}

interface Generation {
  id: number
  platform: string
  content_type: string
  caption_text: string
  created_at: string
}

const platforms = ['Instagram', 'Facebook', 'TikTok', 'Shopee', 'Lazada']
const contentTypes = ['Caption', 'Iklan', 'Copywriting']
const styleOptions = ['santai', 'profesional', 'lucu', 'mewah']

export default function ContentPage() {
  const [products, setProducts] = useState<Product[]>([])
  const [selectedProduct, setSelectedProduct] = useState('')
  const [platform, setPlatform] = useState('Instagram')
  const [contentType, setContentType] = useState('Caption')
  const [description, setDescription] = useState('')
  const [style, setStyle] = useState('santai')
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [result, setResult] = useState<ContentResult | null>(null)
  const [generations, setGenerations] = useState<Generation[]>([])
  const [error, setError] = useState('')

  useEffect(() => {
    async function fetchProducts() {
      const res = await fetch('/api/data/products')
      if (res.ok) {
        const data = await res.json()
        setProducts(data.data ?? data ?? [])
      }
    }
    fetchProducts()
  }, [])

  async function handleGenerate() {
    setLoading(true)
    setError('')
    setResult(null)
    try {
      const res = await fetch('/api/ai/marketing', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_id: selectedProduct || undefined,
          platform,
          content_type: contentType,
          description: description || undefined,
          style,
        }),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.error || 'Gagal generating konten')
      setResult(data)
    } catch (e: any) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }

  async function handleSave() {
    if (!result) return
    setSaving(true)
    try {
      const res = await fetch('/api/data/content-generations', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_id: selectedProduct || null,
          platform,
          content_type: contentType,
          caption_text: result.caption_text,
          hashtags: result.hashtags,
          style,
        }),
      })
      if (!res.ok) throw new Error('Gagal menyimpan')
      setGenerations(prev => [{
        id: Date.now(),
        platform,
        content_type: contentType,
        caption_text: result.caption_text,
        created_at: new Date().toISOString(),
      }, ...prev])
      setResult(null)
    } catch (e: any) {
      setError(e.message)
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900">NusaMarketing</h1>
        <p className="text-sm text-gray-500">Buat konten promosi dengan AI</p>
      </div>

      <Card>
        <div className="flex items-center gap-2 mb-4">
          <Image className="w-5 h-5 text-[#0F9D8E]" />
          <CardTitle>Buat Konten Baru</CardTitle>
        </div>

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">Produk</label>
            <select
              value={selectedProduct}
              onChange={e => setSelectedProduct(e.target.value)}
              className="input-field"
            >
              <option value="">Pilih produk (opsional)</option>
              {products.map(p => (
                <option key={p.id} value={p.id}>{p.name}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">Platform</label>
            <div className="flex flex-wrap gap-2">
              {platforms.map(p => (
                <button
                  key={p}
                  onClick={() => setPlatform(p)}
                  className={`px-4 py-2 rounded-xl text-sm font-medium transition-colors ${
                    platform === p
                      ? 'bg-[#0F9D8E] text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {p}
                </button>
              ))}
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">Tipe Konten</label>
            <div className="flex flex-wrap gap-2">
              {contentTypes.map(ct => (
                <button
                  key={ct}
                  onClick={() => setContentType(ct)}
                  className={`px-4 py-2 rounded-xl text-sm font-medium transition-colors ${
                    contentType === ct
                      ? 'bg-[#0F9D8E] text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {ct}
                </button>
              ))}
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Produk (opsional)</label>
            <textarea
              value={description}
              onChange={e => setDescription(e.target.value)}
              placeholder="Jelaskan produk Anda..."
              rows={3}
              className="input-field resize-none"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">Gaya</label>
            <div className="flex flex-wrap gap-2">
              {styleOptions.map(s => (
                <button
                  key={s}
                  onClick={() => setStyle(s)}
                  className={`px-4 py-2 rounded-xl text-sm font-medium capitalize transition-colors ${
                    style === s
                      ? 'bg-[#0F9D8E] text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {s}
                </button>
              ))}
            </div>
          </div>

          {error && <p className="text-sm text-red-500">{error}</p>}

          <Button onClick={handleGenerate} loading={loading} className="w-full">
            <Sparkles className="w-4 h-4" />
            Buat Konten
          </Button>
        </div>
      </Card>

      {loading && (
        <Card className="flex items-center justify-center gap-3 py-8">
          <LoadingSpinner />
          <span className="text-sm text-gray-500">AI sedang membuat konten...</span>
        </Card>
      )}

      {result && !loading && (
        <AiResultCard title={`Konten ${contentType} untuk ${platform}`}>
          <div className="space-y-4">
            <div>
              <div className="flex items-center gap-1.5 mb-1.5">
                <BookOpen className="w-4 h-4 text-[#0F9D8E]" />
                <span className="text-xs font-medium text-gray-500 uppercase tracking-wide">Caption</span>
              </div>
              <p className="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{result.caption_text}</p>
            </div>

            {result.hashtags && (
              <div>
                <span className="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Hashtag</span>
                <div className="flex flex-wrap gap-1.5">
                  {result.hashtags.split(/[\s,]+/).filter(Boolean).map((tag, i) => (
                    <Badge key={i} variant="info">#{tag.replace(/^#/, '')}</Badge>
                  ))}
                </div>
              </div>
            )}

            {result.cta && (
              <div>
                <span className="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1.5">CTA</span>
                <p className="text-sm text-gray-700">{result.cta}</p>
              </div>
            )}

            {result.platform_tips && (
              <div className="bg-[#e8f5f3]/50 rounded-xl p-3">
                <div className="flex items-center gap-1.5 mb-1">
                  <Lightbulb className="w-4 h-4 text-[#0F9D8E]" />
                  <span className="text-xs font-medium text-[#0F9D8E] uppercase tracking-wide">Tips Platform</span>
                </div>
                <p className="text-sm text-gray-600">{result.platform_tips}</p>
              </div>
            )}

            <div className="flex gap-2 pt-1">
              <Button onClick={handleSave} loading={saving}>
                <Save className="w-4 h-4" />
                Simpan
              </Button>
              <Button variant="outline" onClick={() => setResult(null)}>
                <RefreshCw className="w-4 h-4" />
                Buat Lagi
              </Button>
            </div>
          </div>
        </AiResultCard>
      )}

      <div>
        <h2 className="font-semibold text-gray-900 mb-3">Generasi Sebelumnya</h2>
        {generations.length === 0 ? (
          <EmptyState
            icon={Image}
            title="Belum ada konten"
            description="Konten yang sudah dibuat akan muncul di sini"
          />
        ) : (
          <div className="space-y-2">
            {generations.map(g => (
              <Card key={g.id}>
                <div className="flex items-center gap-2 mb-2">
                  <Badge variant="info">{g.platform}</Badge>
                  <Badge>{g.content_type}</Badge>
                </div>
                <p className="text-sm text-gray-700 line-clamp-2">{g.caption_text}</p>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
