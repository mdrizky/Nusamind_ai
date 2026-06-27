'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { Modal } from '@/components/ui/Modal'
import { MessageSquare, BookOpen, Bookmark, Save, Trash2, Sparkles } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import Link from 'next/link'
import type { CustomerReply } from '@/types'

const tones = [
  { value: 'ramah', label: 'Ramah' },
  { value: 'profesional', label: 'Profesional' },
  { value: 'santai', label: 'Santai' },
]

type Tab = 'balasan' | 'faq' | 'tersimpan'

export default function ReplyPage() {
  const [message, setMessage] = useState('')
  const [tone, setTone] = useState('ramah')
  const [loading, setLoading] = useState(false)
  const [generatedReply, setGeneratedReply] = useState<string | null>(null)
  const [generatedIntent, setGeneratedIntent] = useState<string | null>(null)
  const [replies, setReplies] = useState<CustomerReply[]>([])
  const [repliesLoading, setRepliesLoading] = useState(true)
  const [tab, setTab] = useState<Tab>('balasan')

  const fetchReplies = async () => {
    setRepliesLoading(true)
    try {
      const res = await fetch('/api/data/customer-replies')
      const json = await res.json()
      setReplies(json.data || [])
    } catch {
      setReplies([])
    } finally {
      setRepliesLoading(false)
    }
  }

  useEffect(() => { fetchReplies() }, [])

  const generateReply = async () => {
    if (!message.trim()) return
    setLoading(true)
    setGeneratedReply(null)
    try {
      const res = await fetch('/api/ai/reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message, tone }),
      })
      const json = await res.json()
      if (json.data) {
        setGeneratedReply(json.data.reply)
        setGeneratedIntent(json.data.intent)
      }
    } catch {
      // silent
    } finally {
      setLoading(false)
    }
  }

  const saveReply = async () => {
    if (!generatedReply) return
    try {
      await fetch('/api/data/customer-replies', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          customer_message: message,
          intent: generatedIntent,
          tone,
          generated_reply: generatedReply,
          is_saved: true,
        }),
      })
      fetchReplies()
    } catch {
      // silent
    }
  }

  const deleteReply = async (id: number) => {
    try {
      await fetch(`/api/data/customer-replies?id=${id}`, { method: 'DELETE' })
      fetchReplies()
    } catch {
      // silent
    }
  }

  const tabs: { key: Tab; label: string; icon: any }[] = [
    { key: 'balasan', label: 'Balasan', icon: MessageSquare },
    { key: 'faq', label: 'FAQ', icon: BookOpen },
    { key: 'tersimpan', label: 'Tersimpan', icon: Bookmark },
  ]

  return (
    <div className="p-4 space-y-4">
      <h1 className="text-xl font-bold text-gray-900">NusaReply</h1>

      <Card>
        <CardHeader>
          <CardTitle>Pesan Pelanggan</CardTitle>
        </CardHeader>
        <textarea
          value={message}
          onChange={(e) => setMessage(e.target.value)}
          placeholder="Tulis pesan pelanggan di sini..."
          className="input-field min-h-[100px] resize-none"
        />
        <div className="flex flex-wrap gap-2 mt-3">
          {tones.map((t) => (
            <button
              key={t.value}
              onClick={() => setTone(t.value)}
              className={`px-3 py-1.5 rounded-full text-xs font-medium transition-colors ${
                tone === t.value
                  ? 'bg-[#0F9D8E] text-white'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              }`}
            >
              {t.label}
            </button>
          ))}
        </div>
        <div className="mt-4">
          <Button onClick={generateReply} loading={loading} disabled={!message.trim()}>
            <Sparkles className="w-4 h-4" />
            Buat Balasan
          </Button>
        </div>
      </Card>

      {loading && (
        <div className="flex justify-center py-8">
          <LoadingSpinner />
        </div>
      )}

      {generatedReply && !loading && (
        <div className="space-y-3">
          <AiResultCard title="Balasan AI">
            {generatedReply}
          </AiResultCard>
          <Button variant="outline" onClick={saveReply}>
            <Save className="w-4 h-4" />
            Simpan Balasan
          </Button>
        </div>
      )}

      <div className="flex gap-1 bg-gray-100 rounded-xl p-1">
        {tabs.map((t) => {
          const Icon = t.icon
          return (
            <button
              key={t.key}
              onClick={() => setTab(t.key)}
              className={`flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                tab === t.key ? 'bg-white text-[#0F9D8E] shadow-sm' : 'text-gray-500'
              }`}
            >
              <Icon className="w-4 h-4" />
              {t.label}
            </button>
          )
        })}
      </div>

      {tab === 'balasan' && (
        <div className="space-y-3">
          {repliesLoading ? (
            <div className="flex justify-center py-8"><LoadingSpinner /></div>
          ) : replies.length === 0 ? (
            <EmptyState
              icon={MessageSquare}
              title="Belum ada balasan"
              description="Buat balasan AI untuk mulai menyimpan"
            />
          ) : (
            replies.map((reply) => (
              <Card key={reply.id}>
                <div className="text-sm space-y-2">
                  <div>
                    <span className="text-xs text-gray-400">Pesan:</span>
                    <p className="text-gray-700">{reply.customer_message}</p>
                  </div>
                  <div>
                    <span className="text-xs text-gray-400">Balasan:</span>
                    <p className="text-gray-700">{reply.generated_reply}</p>
                  </div>
                  <div className="flex items-center justify-between pt-2 border-t">
                    <div className="flex items-center gap-2">
                      {reply.tone && <Badge variant="info">{reply.tone}</Badge>}
                      <span className="text-xs text-gray-400">{formatDate(reply.created_at)}</span>
                    </div>
                    <button onClick={() => deleteReply(reply.id)} className="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-500 transition-colors">
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </Card>
            ))
          )}
        </div>
      )}

      {tab === 'faq' && (
        <Card className="py-8">
          <p className="text-center text-gray-500 mb-3">Kelola FAQ bisnis Anda</p>
          <div className="text-center">
            <Link href="/reply/faq">
              <Button variant="primary">
                <BookOpen className="w-4 h-4" />
                Buka FAQ
              </Button>
            </Link>
          </div>
        </Card>
      )}

      {tab === 'tersimpan' && (
        <Card className="py-8">
          <p className="text-center text-gray-500 mb-3">Lihat balasan yang tersimpan</p>
          <div className="text-center">
            <Link href="/reply/saved">
              <Button variant="primary">
                <Bookmark className="w-4 h-4" />
                Buka Tersimpan
              </Button>
            </Link>
          </div>
        </Card>
      )}
    </div>
  )
}
