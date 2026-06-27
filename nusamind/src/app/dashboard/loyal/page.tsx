'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { Modal } from '@/components/ui/Modal'
import { Users, Plus, Sparkles, Edit2, Trash2, Phone, MapPin, ShoppingBag, DollarSign, MessageCircle, X } from 'lucide-react'
import { formatDate, formatCurrency } from '@/lib/utils'
import type { Customer } from '@/types'

interface AiFollowUp {
  follow_up_message: string
  subject: string
  segment_note: string
  next_action: string
}

export default function LoyalPage() {
  const [customers, setCustomers] = useState<Customer[]>([])
  const [loading, setLoading] = useState(true)
  const [modalOpen, setModalOpen] = useState(false)
  const [editCustomer, setEditCustomer] = useState<Customer | null>(null)
  const [name, setName] = useState('')
  const [phone, setPhone] = useState('')
  const [address, setAddress] = useState('')
  const [notes, setNotes] = useState('')
  const [saving, setSaving] = useState(false)
  const [aiModalOpen, setAiModalOpen] = useState(false)
  const [aiLoading, setAiLoading] = useState(false)
  const [aiResult, setAiResult] = useState<AiFollowUp | null>(null)

  const fetchCustomers = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/customers')
      const json = await res.json()
      setCustomers(json.data || [])
    } catch {
      setCustomers([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchCustomers() }, [])

  const getSegmentBadge = (segment: string) => {
    switch (segment) {
      case 'vip': return { label: 'VIP', variant: 'success' as const }
      case 'regular': return { label: 'Reguler', variant: 'info' as const }
      default: return { label: 'Baru', variant: 'warning' as const }
    }
  }

  const openAddModal = () => {
    setEditCustomer(null)
    setName('')
    setPhone('')
    setAddress('')
    setNotes('')
    setModalOpen(true)
  }

  const openEditModal = (c: Customer) => {
    setEditCustomer(c)
    setName(c.name)
    setPhone(c.phone || '')
    setAddress(c.address || '')
    setNotes(c.notes || '')
    setModalOpen(true)
  }

  const saveCustomer = async () => {
    if (!name.trim()) return
    setSaving(true)
    try {
      const body = { name, phone: phone || null, address: address || null, notes: notes || null }
      if (editCustomer) {
        await fetch(`/api/data/customers?id=${editCustomer.id}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
      } else {
        await fetch('/api/data/customers', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
      }
      setModalOpen(false)
      fetchCustomers()
    } catch {
      // silent
    } finally {
      setSaving(false)
    }
  }

  const deleteCustomer = async (id: number) => {
    try {
      await fetch(`/api/data/customers?id=${id}`, { method: 'DELETE' })
      fetchCustomers()
    } catch {
      // silent
    }
  }

  const generateFollowUp = async (customer: Customer) => {
    setAiLoading(true)
    setAiResult(null)
    setAiModalOpen(true)
    try {
      const res = await fetch('/api/ai/loyal', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(customer),
      })
      const json = await res.json()
      if (json.data) setAiResult(json.data)
    } catch {
      // silent
    } finally {
      setAiLoading(false)
    }
  }

  return (
    <div className="p-4 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">NusaLoyal</h1>
        <Button size="sm" onClick={openAddModal}>
          <Plus className="w-4 h-4" />
          Tambah Pelanggan
        </Button>
      </div>

      {loading ? (
        <div className="flex justify-center py-16"><LoadingSpinner /></div>
      ) : customers.length === 0 ? (
        <EmptyState
          icon={Users}
          title="Belum ada pelanggan"
          description="Tambahkan pelanggan untuk mulai mengelola loyalitas"
        />
      ) : (
        <div className="space-y-3">
          {customers.map((c) => {
            const segment = getSegmentBadge(c.segment)
            return (
              <Card key={c.id}>
                <div className="flex items-start justify-between gap-3">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <h4 className="font-medium text-gray-900">{c.name}</h4>
                      <Badge variant={segment.variant}>{segment.label}</Badge>
                    </div>
                    <div className="mt-2 space-y-1">
                      {c.phone && (
                        <p className="text-xs text-gray-500 flex items-center gap-1.5">
                          <Phone className="w-3 h-3" /> {c.phone}
                        </p>
                      )}
                      {c.address && (
                        <p className="text-xs text-gray-500 flex items-center gap-1.5">
                          <MapPin className="w-3 h-3" /> {c.address}
                        </p>
                      )}
                      <div className="flex items-center gap-3 pt-1">
                        <span className="text-xs text-gray-400 flex items-center gap-1">
                          <ShoppingBag className="w-3 h-3" /> {c.total_orders} pesanan
                        </span>
                        <span className="text-xs text-gray-400 flex items-center gap-1">
                          <DollarSign className="w-3 h-3" /> {formatCurrency(c.total_spent)}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div className="flex flex-col gap-1">
                    <button
                      onClick={() => generateFollowUp(c)}
                      className="p-1.5 hover:bg-[#0F9D8E]/10 rounded-lg text-gray-400 hover:text-[#0F9D8E] transition-colors"
                      title="AI Follow-up"
                    >
                      <Sparkles className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => openEditModal(c)}
                      className="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                    >
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => deleteCustomer(c.id)}
                      className="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-500 transition-colors"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </Card>
            )
          })}
        </div>
      )}

      <Modal open={modalOpen} onClose={() => setModalOpen(false)} title={editCustomer ? 'Edit Pelanggan' : 'Tambah Pelanggan'}>
        <div className="space-y-4">
          <Input label="Nama" value={name} onChange={(e) => setName(e.target.value)} placeholder="Nama pelanggan" />
          <Input label="No. Telepon" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="08xxxx" />
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea
              value={address}
              onChange={(e) => setAddress(e.target.value)}
              placeholder="Alamat pelanggan"
              className="input-field min-h-[60px] resize-none"
            />
          </div>
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              placeholder="Catatan tambahan"
              className="input-field min-h-[60px] resize-none"
            />
          </div>
          <Button className="w-full" onClick={saveCustomer} loading={saving} disabled={!name.trim()}>
            {editCustomer ? 'Simpan Perubahan' : 'Tambah Pelanggan'}
          </Button>
        </div>
      </Modal>

      <Modal open={aiModalOpen} onClose={() => { setAiModalOpen(false); setAiResult(null) }} title="AI Follow-up">
        {aiLoading ? (
          <div className="flex justify-center py-8"><LoadingSpinner /></div>
        ) : aiResult ? (
          <div className="space-y-4">
            <div>
              <span className="text-xs font-medium text-gray-500">Subjek</span>
              <p className="text-sm font-medium text-gray-900 mt-0.5">{aiResult.subject}</p>
            </div>
            <div>
              <span className="text-xs font-medium text-gray-500">Pesan Follow-up</span>
              <div className="mt-1 p-3 bg-gray-50 rounded-xl">
                <p className="text-sm text-gray-700 whitespace-pre-wrap">{aiResult.follow_up_message}</p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Badge variant="info">{aiResult.segment_note}</Badge>
            </div>
            <div>
              <span className="text-xs font-medium text-gray-500">Tindakan Selanjutnya</span>
              <p className="text-sm text-gray-700 mt-0.5">{aiResult.next_action}</p>
            </div>
          </div>
        ) : null}
      </Modal>
    </div>
  )
}
