'use client'

import { useState, useRef, useEffect } from 'react'
import { Button } from '@/components/ui/Button'
import { cn, formatDate } from '@/lib/utils'
import { Send, Trash2, Bot, User, Sparkles } from 'lucide-react'

interface ChatMessage {
  role: 'user' | 'assistant'
  content: string
  timestamp: string
}

const MAX_HISTORY = 20

export default function NusaCoachPage() {
  const [messages, setMessages] = useState<ChatMessage[]>([])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const [typing, setTyping] = useState('')
  const messagesEndRef = useRef<HTMLDivElement>(null)
  const inputRef = useRef<HTMLInputElement>(null)

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' })
  }

  useEffect(() => {
    scrollToBottom()
  }, [messages, typing])

  const sendMessage = async () => {
    const text = input.trim()
    if (!text || loading) return

    const userMsg: ChatMessage = { role: 'user', content: text, timestamp: new Date().toISOString() }
    const updated = [...messages, userMsg]
    setMessages(updated)
    setInput('')
    setLoading(true)
    setTyping('Sedang mengetik...')

    try {
      const history = updated.slice(-MAX_HISTORY).map((m) => ({
        role: m.role,
        content: m.content,
      }))

      const res = await fetch('/api/ai/coach', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: text, history }),
      })

      const data = await res.json()
      const reply = data.data?.reply ?? data.reply ?? data.message ?? 'Maaf, terjadi kesalahan.'

      setMessages((prev) => [
        ...prev,
        { role: 'assistant', content: reply, timestamp: new Date().toISOString() },
      ])
    } catch {
      setMessages((prev) => [
        ...prev,
        {
          role: 'assistant',
          content: 'Maaf, terjadi kesalahan. Silakan coba lagi.',
          timestamp: new Date().toISOString(),
        },
      ])
    } finally {
      setLoading(false)
      setTyping('')
      inputRef.current?.focus()
    }
  }

  const clearChat = () => {
    setMessages([])
  }

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      sendMessage()
    }
  }

  return (
    <div className="flex flex-col h-[calc(100vh-7rem)]">
      <div className="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-white">
        <div className="flex items-center gap-2">
          <Bot className="w-5 h-5 text-[#0F9D8E]" />
          <div>
            <h1 className="text-base font-bold text-gray-900 font-poppins">NusaCoach</h1>
            <p className="text-[10px] text-gray-400">Mentor AI bisnis Anda</p>
          </div>
        </div>
        <button
          onClick={clearChat}
          className="p-2 hover:bg-gray-100 rounded-xl transition-colors text-gray-400 hover:text-red-500"
          title="Hapus Percakapan"
        >
          <Trash2 className="w-4 h-4" />
        </button>
      </div>

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {messages.length === 0 && !typing && (
          <div className="flex flex-col items-center justify-center h-full text-center py-12">
            <Sparkles className="w-10 h-10 text-[#0F9D8E] mb-4" />
            <h2 className="text-lg font-semibold text-gray-900 font-poppins">Halo! Ada yang bisa saya bantu?</h2>
            <p className="text-sm text-gray-500 mt-2 max-w-xs">
              Tanyakan apapun tentang bisnis Anda — strategi, analisis, atau ide baru.
            </p>
          </div>
        )}

        {messages.map((msg, i) => (
          <div key={i} className={cn('flex', msg.role === 'user' ? 'justify-end' : 'justify-start')}>
            <div className={cn(
              'max-w-[85%] rounded-2xl px-4 py-2.5',
              msg.role === 'user'
                ? 'bg-[#0F9D8E] text-white rounded-br-md'
                : 'bg-gray-100 text-gray-800 rounded-bl-md',
            )}>
              <p className="text-sm whitespace-pre-wrap">{msg.content}</p>
              <p className={cn(
                'text-[10px] mt-1',
                msg.role === 'user' ? 'text-white/60' : 'text-gray-400',
              )}>
                {new Date(msg.timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
              </p>
            </div>
          </div>
        ))}

        {typing && (
          <div className="flex justify-start">
            <div className="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
              <div className="flex gap-1">
                <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }} />
                <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }} />
                <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }} />
              </div>
            </div>
          </div>
        )}

        <div ref={messagesEndRef} />
      </div>

      <div className="sticky bottom-0 bg-white border-t border-gray-100 p-3">
        <div className="flex items-center gap-2 max-w-lg mx-auto">
          <input
            ref={inputRef}
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={handleKeyDown}
            placeholder="Ketik pesan..."
            className="input-field flex-1"
            disabled={loading}
          />
          <button
            onClick={sendMessage}
            disabled={!input.trim() || loading}
            className="w-11 h-11 rounded-xl bg-[#0F9D8E] text-white flex items-center justify-center shrink-0 hover:bg-[#0c8578] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Send className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  )
}
