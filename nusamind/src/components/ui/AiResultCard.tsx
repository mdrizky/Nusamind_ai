'use client'
import { useState } from 'react'
import { Copy, Check, Sparkles } from 'lucide-react'
import { Card } from './Card'

export function AiResultCard({ title, children }: { title: string; children: React.ReactNode }) {
  const [copied, setCopied] = useState(false)
  const text = typeof children === 'string' ? children : ''

  const copyText = async () => {
    if (text) {
      await navigator.clipboard.writeText(text)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    }
  }

  return (
    <Card className="border-[#0F9D8E]/20 bg-[#e8f5f3]/30">
      <div className="flex items-center justify-between mb-3">
        <div className="flex items-center gap-2">
          <Sparkles className="w-4 h-4 text-[#0F9D8E]" />
          <h4 className="font-medium text-sm text-gray-700">{title}</h4>
        </div>
        {text && (
          <button onClick={copyText} className="p-1.5 hover:bg-white rounded-lg transition-colors">
            {copied ? <Check className="w-4 h-4 text-green-600" /> : <Copy className="w-4 h-4 text-gray-400" />}
          </button>
        )}
      </div>
      <div className="text-sm text-gray-600 whitespace-pre-wrap">{children}</div>
    </Card>
  )
}
