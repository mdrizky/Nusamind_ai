'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { Sparkles, Save, Trash2, Megaphone, Copy, Check, Lightbulb, Hash } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import type { CampaignPlan, Product } from '@/types'

interface CampaignResult {
  campaign_name: string
  caption: string
  broadcast_message: string
  tips: string[]
  hashtags: string[]
}

export default function CampaignPage() {
  const [goal, setGoal] = useState('')
  const [targetProductId, setTargetProductId] = useState<number | ''>('')
  const [products, setProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(false)
  const [campaigns, setCampaigns] = useState<CampaignPlan[]>([])
  const [campaignsLoading, setCampaignsLoading] = useState(true)
  const [result, setResult] = useState<CampaignResult | null>(null)
  const [copiedCaption, setCopiedCaption] = useState(false)
  const [copiedBroadcast, setCopiedBroadcast] = useState(false)

  const fetchProducts = async () => {
    try {
      const res = await fetch('/api/data/products')
      const json = await res.json()
      setProducts(json.data || [])
    } catch {
      setProducts([])
    }
  }

  const fetchCampaigns = async () => {
    setCampaignsLoading(true)
    try {
      const res = await fetch('/api/data/campaigns')
      const json = await res.json()
      setCampaigns(json.data || [])
    } catch {
      setCampaigns([])
    } finally {
      setCampaignsLoading(false)
    }
  }

  useEffect(() => {
    fetchProducts()
    fetchCampaigns()
  }, [])

  const generateCampaign = async () => {
    if (!goal.trim()) return
    setLoading(true)
    setResult(null)
    try {
      const res = await fetch('/api/ai/campaign', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ goal, target_product_id: targetProductId || null }),
      })
      const json = await res.json()
      if (json.data) setResult(json.data)
    } catch {
      // silent
    } finally {
      setLoading(false)
    }
  }

  const saveCampaign = async () => {
    if (!result) return
    try {
      await fetch('/api/data/campaigns', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          campaign_goal: goal,
          target_product_id: targetProductId || null,
          campaign_name: result.campaign_name,
          caption: result.caption,
          broadcast_message: result.broadcast_message,
          plan_result: JSON.stringify({ tips: result.tips, hashtags: result.hashtags }),
        }),
      })
      fetchCampaigns()
    } catch {
      // silent
    }
  }

  const deleteCampaign = async (id: number) => {
    try {
      await fetch(`/api/data/campaigns?id=${id}`, { method: 'DELETE' })
      fetchCampaigns()
    } catch {
      // silent
    }
  }

  const copyText = async (text: string, target: 'caption' | 'broadcast') => {
    await navigator.clipboard.writeText(text)
    if (target === 'caption') {
      setCopiedCaption(true)
      setTimeout(() => setCopiedCaption(false), 2000)
    } else {
      setCopiedBroadcast(true)
      setTimeout(() => setCopiedBroadcast(false), 2000)
    }
  }

  return (
    <div className="p-4 space-y-4">
      <h1 className="text-xl font-bold text-gray-900">NusaCampaign</h1>

      <Card>
        <CardHeader>
          <CardTitle>Buat Campaign Baru</CardTitle>
        </CardHeader>
        <div className="space-y-4">
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Tujuan Campaign</label>
            <textarea
              value={goal}
              onChange={(e) => setGoal(e.target.value)}
              placeholder="Contoh: Meningkatkan penjualan produk skincare di bulan Desember"
              className="input-field min-h-[80px] resize-none"
            />
          </div>
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Target Produk (opsional)</label>
            <select
              value={targetProductId}
              onChange={(e) => setTargetProductId(e.target.value ? parseInt(e.target.value) : '')}
              className="input-field"
            >
              <option value="">Pilih produk</option>
              {products.map((p) => (
                <option key={p.id} value={p.id}>{p.name}</option>
              ))}
            </select>
          </div>
          <Button onClick={generateCampaign} loading={loading} disabled={!goal.trim()}>
            <Sparkles className="w-4 h-4" />
            Buat Campaign
          </Button>
        </div>
      </Card>

      {loading && (
        <div className="flex justify-center py-8">
          <LoadingSpinner />
        </div>
      )}

      {result && !loading && (
        <div className="space-y-3">
          <Card>
            <CardHeader>
              <CardTitle>{result.campaign_name}</CardTitle>
            </CardHeader>

            <div className="space-y-4">
              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs font-medium text-gray-500">Caption</span>
                  <button onClick={() => copyText(result.caption, 'caption')} className="p-1 hover:bg-gray-100 rounded-lg">
                    {copiedCaption ? <Check className="w-3.5 h-3.5 text-green-600" /> : <Copy className="w-3.5 h-3.5 text-gray-400" />}
                  </button>
                </div>
                <p className="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-xl p-3">{result.caption}</p>
              </div>

              <div>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-xs font-medium text-gray-500">Pesan Broadcast</span>
                  <button onClick={() => copyText(result.broadcast_message, 'broadcast')} className="p-1 hover:bg-gray-100 rounded-lg">
                    {copiedBroadcast ? <Check className="w-3.5 h-3.5 text-green-600" /> : <Copy className="w-3.5 h-3.5 text-gray-400" />}
                  </button>
                </div>
                <p className="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-xl p-3">{result.broadcast_message}</p>
              </div>

              {result.tips.length > 0 && (
                <div>
                  <span className="text-xs font-medium text-gray-500 flex items-center gap-1 mb-2">
                    <Lightbulb className="w-3.5 h-3.5" /> Tips
                  </span>
                  <ul className="space-y-1">
                    {result.tips.map((tip, i) => (
                      <li key={i} className="text-sm text-gray-600 flex items-start gap-2">
                        <span className="text-[#0F9D8E] mt-0.5">•</span>
                        {tip}
                      </li>
                    ))}
                  </ul>
                </div>
              )}

              {result.hashtags.length > 0 && (
                <div>
                  <span className="text-xs font-medium text-gray-500 flex items-center gap-1 mb-2">
                    <Hash className="w-3.5 h-3.5" /> Hashtag
                  </span>
                  <div className="flex flex-wrap gap-1.5">
                    {result.hashtags.map((tag, i) => (
                      <Badge key={i} variant="info">{tag}</Badge>
                    ))}
                  </div>
                </div>
              )}
            </div>

            <div className="mt-4">
              <Button onClick={saveCampaign}>
                <Save className="w-4 h-4" />
                Simpan Campaign
              </Button>
            </div>
          </Card>
        </div>
      )}

      <Card>
        <CardHeader>
          <CardTitle>Campaign Tersimpan</CardTitle>
        </CardHeader>
        {campaignsLoading ? (
          <div className="flex justify-center py-8"><LoadingSpinner /></div>
        ) : campaigns.length === 0 ? (
          <EmptyState icon={Megaphone} title="Belum ada campaign" description="Buat campaign AI untuk memulai" />
        ) : (
          <div className="space-y-3">
            {campaigns.map((c) => (
              <Card key={c.id}>
                <div className="flex items-start justify-between gap-3">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <h4 className="font-medium text-sm text-gray-900">{c.campaign_name || 'Campaign'}</h4>
                      <Badge variant={c.is_active ? 'success' : 'default'}>
                        {c.is_active ? 'Aktif' : 'Tidak Aktif'}
                      </Badge>
                    </div>
                    <p className="text-xs text-gray-500 mt-1 line-clamp-2">{c.campaign_goal}</p>
                    <p className="text-xs text-gray-400 mt-1">{formatDate(c.created_at)}</p>
                  </div>
                  <button
                    onClick={() => deleteCampaign(c.id)}
                    className="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-500 transition-colors shrink-0"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>
              </Card>
            ))}
          </div>
        )}
      </Card>
    </div>
  )
}
