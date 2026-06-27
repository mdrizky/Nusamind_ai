'use client'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { Card } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { LoadingSpinner, PageLoading } from '@/components/ui/LoadingSpinner'
import { Bookmark, Copy, Check, Trash2 } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import type { CustomerReply } from '@/types'

export default function SavedRepliesPage() {
  const [replies, setReplies] = useState<CustomerReply[]>([])
  const [loading, setLoading] = useState(true)
  const [copiedId, setCopiedId] = useState<number | null>(null)

  const fetchSaved = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/customer-replies?is_saved=true')
      const json = await res.json()
      setReplies(json.data || [])
    } catch {
      setReplies([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchSaved() }, [])

  const copyText = async (text: string, id: number) => {
    await navigator.clipboard.writeText(text)
    setCopiedId(id)
    setTimeout(() => setCopiedId(null), 2000)
  }

  const deleteReply = async (id: number) => {
    try {
      await fetch(`/api/data/customer-replies?id=${id}`, { method: 'DELETE' })
      fetchSaved()
    } catch {
      // silent
    }
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Balasan Tersimpan</h1>

      {replies.length === 0 ? (
        <EmptyState
          icon={Bookmark}
          title="Belum ada balasan yang disimpan"
          description="Simpan balasan AI favorit Anda di sini"
        />
      ) : (
        <div className="space-y-3">
          {replies.map((reply) => (
            <Card key={reply.id}>
              <div className="text-sm space-y-2">
                <div>
                  <span className="text-xs text-gray-400">Pesan Pelanggan:</span>
                  <p className="text-gray-700 mt-0.5">{reply.customer_message}</p>
                </div>
                <div>
                  <span className="text-xs text-gray-400">Balasan:</span>
                  <p className="text-gray-700 mt-0.5 whitespace-pre-wrap">{reply.generated_reply}</p>
                </div>
                <div className="flex items-center justify-between pt-2 border-t">
                  <div className="flex items-center gap-2">
                    {reply.tone && <Badge variant="info">{reply.tone}</Badge>}
                    <span className="text-xs text-gray-400">{formatDate(reply.created_at)}</span>
                  </div>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => copyText(reply.generated_reply, reply.id)}
                      className="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                    >
                      {copiedId === reply.id ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4" />}
                    </button>
                    <button
                      onClick={() => deleteReply(reply.id)}
                      className="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-500 transition-colors"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </Card>
          ))}
        </div>
      )}
    </div>
  )
}
