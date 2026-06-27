'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { Modal } from '@/components/ui/Modal'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { Plus, Edit3, Trash2, Folder } from 'lucide-react'
import type { Category } from '@/types'

export default function AdminCategoriesPage() {
  const [categories, setCategories] = useState<Category[]>([])
  const [loading, setLoading] = useState(true)
  const [modalOpen, setModalOpen] = useState(false)
  const [editing, setEditing] = useState<Category | null>(null)
  const [saving, setSaving] = useState(false)
  const [form, setForm] = useState({ name: '', icon: '' })

  const fetchCategories = async () => {
    const res = await fetch('/api/data/admin/categories')
    if (res.ok) {
      const data = await res.json()
      setCategories(data.data || [])
    }
    setLoading(false)
  }

  useEffect(() => { fetchCategories() }, [])

  const openAdd = () => {
    setEditing(null)
    setForm({ name: '', icon: '' })
    setModalOpen(true)
  }

  const openEdit = (cat: Category) => {
    setEditing(cat)
    setForm({ name: cat.name, icon: cat.icon })
    setModalOpen(true)
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setSaving(true)
    try {
      const url = '/api/data/admin/categories'
      const method = editing ? 'PUT' : 'POST'
      const body = editing ? { id: editing.id, ...form } : form

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      })
      if (res.ok) {
        setModalOpen(false)
        fetchCategories()
      }
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm('Hapus kategori ini?')) return
    const res = await fetch(`/api/data/admin/categories?id=${id}`, { method: 'DELETE' })
    if (res.ok) fetchCategories()
  }

  if (loading) return <PageLoading />

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Kategori</h1>
        <Button onClick={openAdd}>
          <Plus className="w-4 h-4" />
          Tambah
        </Button>
      </div>

      {categories.length === 0 ? (
        <Card>
          <EmptyState title="Belum ada kategori" />
        </Card>
      ) : (
        <div className="grid gap-3">
          {categories.map(cat => (
            <Card key={cat.id} className="flex items-center gap-3 py-3 px-4">
              <div className="w-10 h-10 rounded-xl bg-[#e8f5f3] flex items-center justify-center text-xl">
                {cat.icon || <Folder className="w-5 h-5 text-[#0F9D8E]" />}
              </div>
              <div className="flex-1">
                <p className="font-medium text-gray-900">{cat.name}</p>
              </div>
              <div className="flex gap-1">
                <button onClick={() => openEdit(cat)} className="p-2 hover:bg-gray-100 rounded-lg">
                  <Edit3 className="w-4 h-4 text-gray-400" />
                </button>
                <button onClick={() => handleDelete(cat.id)} className="p-2 hover:bg-red-50 rounded-lg">
                  <Trash2 className="w-4 h-4 text-red-400" />
                </button>
              </div>
            </Card>
          ))}
        </div>
      )}

      <Modal open={modalOpen} onClose={() => setModalOpen(false)} title={editing ? 'Edit Kategori' : 'Tambah Kategori'}>
        <form onSubmit={handleSave} className="space-y-4">
          <Input label="Nama Kategori" value={form.name} onChange={e => setForm(f => ({ ...f, name: e.target.value }))} required />
          <Input label="Icon (emoji)" value={form.icon} onChange={e => setForm(f => ({ ...f, icon: e.target.value }))} placeholder="🛍️" />
          <Button type="submit" className="w-full" loading={saving}>
            {editing ? 'Simpan' : 'Tambah'}
          </Button>
        </form>
      </Modal>
    </div>
  )
}
