'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { Modal } from '@/components/ui/Modal'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { Store, Package, MapPin, Edit3 } from 'lucide-react'
import type { Business, Product } from '@/types'

export default function BusinessPage() {
  const [business, setBusiness] = useState<Business | null>(null)
  const [products, setProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(true)
  const [editOpen, setEditOpen] = useState(false)
  const [saving, setSaving] = useState(false)
  const [form, setForm] = useState({
    business_name: '',
    city: '',
    description: '',
    brand_tone: '',
    open_hours: '',
    shipping_info: '',
    whatsapp_number: '',
  })

  useEffect(() => {
    async function loadData() {
      const [busRes, prodRes] = await Promise.all([
        fetch('/api/data/business'),
        fetch('/api/data/products'),
      ])
      if (busRes.ok) {
        const data = await busRes.json()
        setBusiness(data.data)
        if (data.data) {
          setForm({
            business_name: data.data.business_name || '',
            city: data.data.city || '',
            description: data.data.description || '',
            brand_tone: data.data.brand_tone || '',
            open_hours: data.data.open_hours || '',
            shipping_info: data.data.shipping_info || '',
            whatsapp_number: data.data.whatsapp_number || '',
          })
        }
      }
      if (prodRes.ok) {
        const data = await prodRes.json()
        setProducts(data.data || [])
      }
      setLoading(false)
    }
    loadData()
  }, [])

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setSaving(true)
    try {
      const res = await fetch('/api/data/business', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form),
      })
      if (res.ok) {
        const data = await res.json()
        setBusiness(data.data)
        setEditOpen(false)
      }
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <h1 className="text-xl font-bold text-gray-900 font-poppins">Profil Usaha</h1>

      {!business ? (
        <EmptyState icon={Store} title="Belum ada data usaha" description="Lengkapi profil usaha Anda" />
      ) : (
        <Card>
          <CardHeader>
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-2xl bg-[#e8f5f3] flex items-center justify-center">
                <Store className="w-6 h-6 text-[#0F9D8E]" />
              </div>
              <div>
                <CardTitle>{business.business_name}</CardTitle>
                <div className="flex items-center gap-1 text-sm text-gray-500 mt-0.5">
                  <MapPin className="w-3.5 h-3.5" />
                  {business.city || 'Kota tidak diisi'}
                </div>
              </div>
            </div>
            <button
              onClick={() => setEditOpen(true)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <Edit3 className="w-4 h-4 text-gray-400" />
            </button>
          </CardHeader>

          {business.description && (
            <p className="text-sm text-gray-600">{business.description}</p>
          )}

          <div className="mt-4 space-y-3 text-sm">
            {business.brand_tone && (
              <div>
                <span className="text-xs text-gray-400 uppercase font-medium">Brand Tone</span>
                <p className="text-gray-700">{business.brand_tone}</p>
              </div>
            )}
            {business.whatsapp_number && (
              <div>
                <span className="text-xs text-gray-400 uppercase font-medium">WhatsApp</span>
                <p className="text-gray-700">{business.whatsapp_number}</p>
              </div>
            )}
            {business.open_hours && (
              <div>
                <span className="text-xs text-gray-400 uppercase font-medium">Jam Operasional</span>
                <p className="text-gray-700">{business.open_hours}</p>
              </div>
            )}
            {business.shipping_info && (
              <div>
                <span className="text-xs text-gray-400 uppercase font-medium">Info Pengiriman</span>
                <p className="text-gray-700">{business.shipping_info}</p>
              </div>
            )}
          </div>
        </Card>
      )}

      <div>
        <div className="flex items-center justify-between mb-3">
          <h2 className="font-semibold text-gray-900">Produk ({products.length})</h2>
        </div>
        {products.length === 0 ? (
          <EmptyState icon={Package} title="Belum ada produk" />
        ) : (
          <div className="space-y-2">
            {products.map(product => (
              <Card key={product.id} className="flex items-center gap-3 py-3 px-4">
                <div className="w-10 h-10 rounded-xl bg-[#e8f5f3] flex items-center justify-center">
                  <Package className="w-5 h-5 text-[#0F9D8E]" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900">{product.name}</p>
                  <p className="text-xs text-gray-400">Stok: {product.stock} {product.unit}</p>
                </div>
              </Card>
            ))}
          </div>
        )}
      </div>

      <Modal open={editOpen} onClose={() => setEditOpen(false)} title="Edit Profil Usaha">
        <form onSubmit={handleSave} className="space-y-4">
          <Input label="Nama Usaha" value={form.business_name} onChange={e => setForm(f => ({ ...f, business_name: e.target.value }))} required />
          <Input label="Kota" value={form.city} onChange={e => setForm(f => ({ ...f, city: e.target.value }))} />
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea
              className="input-field"
              rows={3}
              value={form.description}
              onChange={e => setForm(f => ({ ...f, description: e.target.value }))}
            />
          </div>
          <Input label="Brand Tone" value={form.brand_tone} onChange={e => setForm(f => ({ ...f, brand_tone: e.target.value }))} />
          <Input label="Jam Operasional" value={form.open_hours} onChange={e => setForm(f => ({ ...f, open_hours: e.target.value }))} />
          <Input label="Info Pengiriman" value={form.shipping_info} onChange={e => setForm(f => ({ ...f, shipping_info: e.target.value }))} />
          <Input label="Nomor WhatsApp" value={form.whatsapp_number} onChange={e => setForm(f => ({ ...f, whatsapp_number: e.target.value }))} />
          <Button type="submit" className="w-full" loading={saving}>Simpan</Button>
        </form>
      </Modal>
    </div>
  )
}
