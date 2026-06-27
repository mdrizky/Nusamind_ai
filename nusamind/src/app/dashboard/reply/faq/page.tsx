'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner, PageLoading } from '@/components/ui/LoadingSpinner'
import { Modal } from '@/components/ui/Modal'
import { Plus, Trash2, HelpCircle } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import type { BusinessFaq } from '@/types'

export default function FaqPage() {
  const [faqs, setFaqs] = useState<BusinessFaq[]>([])
  const [loading, setLoading] = useState(true)
  const [modalOpen, setModalOpen] = useState(false)
  const [question, setQuestion] = useState('')
  const [answer, setAnswer] = useState('')
  const [category, setCategory] = useState('')
  const [saving, setSaving] = useState(false)

  const fetchFaqs = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/faqs')
      const json = await res.json()
      setFaqs(json.data || [])
    } catch {
      setFaqs([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchFaqs() }, [])

  const createFaq = async () => {
    if (!question.trim() || !answer.trim()) return
    setSaving(true)
    try {
      await fetch('/api/data/faqs', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question, answer, category: category || null }),
      })
      setModalOpen(false)
      setQuestion('')
      setAnswer('')
      setCategory('')
      fetchFaqs()
    } catch {
      // silent
    } finally {
      setSaving(false)
    }
  }

  const deleteFaq = async (id: number) => {
    try {
      await fetch(`/api/data/faqs?id=${id}`, { method: 'DELETE' })
      fetchFaqs()
    } catch {
      // silent
    }
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">FAQ</h1>
        <Button size="sm" onClick={() => setModalOpen(true)}>
          <Plus className="w-4 h-4" />
          Tambah FAQ
        </Button>
      </div>

      {faqs.length === 0 ? (
        <EmptyState
          icon={HelpCircle}
          title="Belum ada FAQ"
          description="Tambahkan FAQ untuk membantu pelanggan"
        />
      ) : (
        <div className="space-y-3">
          {faqs.map((faq) => (
            <Card key={faq.id}>
              <div className="flex items-start justify-between gap-3">
                <div className="flex-1 min-w-0 space-y-1">
                  <h4 className="font-medium text-gray-900 text-sm">{faq.question}</h4>
                  <p className="text-sm text-gray-500 line-clamp-2">{faq.answer}</p>
                  <div className="flex items-center gap-2 pt-1">
                    {faq.category && <Badge variant="info">{faq.category}</Badge>}
                    <span className="text-xs text-gray-400">{formatDate(faq.created_at)}</span>
                  </div>
                </div>
                <button
                  onClick={() => deleteFaq(faq.id)}
                  className="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-500 transition-colors shrink-0"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            </Card>
          ))}
        </div>
      )}

      <Modal open={modalOpen} onClose={() => setModalOpen(false)} title="Tambah FAQ">
        <div className="space-y-4">
          <Input
            label="Pertanyaan"
            value={question}
            onChange={(e) => setQuestion(e.target.value)}
            placeholder="Masukkan pertanyaan"
          />
          <div className="space-y-1.5">
            <label className="block text-sm font-medium text-gray-700">Jawaban</label>
            <textarea
              value={answer}
              onChange={(e) => setAnswer(e.target.value)}
              placeholder="Masukkan jawaban"
              className="input-field min-h-[100px] resize-none"
            />
          </div>
          <Input
            label="Kategori (opsional)"
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            placeholder="Misal: Pengiriman, Pembayaran"
          />
          <Button className="w-full" onClick={createFaq} loading={saving} disabled={!question.trim() || !answer.trim()}>
            Simpan FAQ
          </Button>
        </div>
      </Modal>
    </div>
  )
}
