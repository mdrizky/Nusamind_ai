'use client'

import { useState, useRef } from 'react'
import { formatCurrency } from '@/lib/utils'
import { Button } from '@/components/ui/Button'
import { Card, CardTitle } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Badge } from '@/components/ui/Badge'
import { LoadingSpinner } from '@/components/ui/LoadingSpinner'
import { AiResultCard } from '@/components/ui/AiResultCard'
import { EmptyState } from '@/components/ui/EmptyState'
import { ArrowUp, ArrowDown, CheckCircle, Wallet, RotateCcw } from 'lucide-react'

interface ParsedResult {
  type: 'pemasukan' | 'pengeluaran'
  item_name: string
  amount: number
  quantity: number | null
  confidence: number
}

interface SavedTransaction {
  id: number
  type: 'pemasukan' | 'pengeluaran'
  item_name: string
  amount: number
}

export default function FinancePage() {
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [result, setResult] = useState<ParsedResult | null>(null)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)
  const [savedTransactions, setSavedTransactions] = useState<SavedTransaction[]>([])
  const inputRef = useRef<HTMLInputElement>(null)

  async function handleParse() {
    if (!input.trim()) return
    setLoading(true)
    setError('')
    setResult(null)
    setSuccess(false)

    try {
      const res = await fetch('/api/ai/finance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text: input }),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.error || 'Gagal memproses')
      if (data.confidence < 0.6) {
        setError('AI kurang yakin dengan input Anda. Silakan coba dengan format yang lebih jelas.')
        setLoading(false)
        return
      }
      setResult(data)
    } catch (e: any) {
      setError(e.message || 'Terjadi kesalahan')
    } finally {
      setLoading(false)
    }
  }

  async function handleSave() {
    if (!result) return
    setSaving(true)
    try {
      const res = await fetch('/api/data/transactions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          type: result.type,
          item_name: result.item_name,
          amount: result.amount,
          quantity: result.quantity,
          raw_input: input,
        }),
      })
      if (!res.ok) throw new Error('Gagal menyimpan')
      setSuccess(true)
      setSavedTransactions(prev => [{
        id: Date.now(),
        type: result.type,
        item_name: result.item_name,
        amount: result.amount,
      }, ...prev])
      setInput('')
      setResult(null)
      inputRef.current?.focus()
    } catch (e: any) {
      setError(e.message || 'Gagal menyimpan')
    } finally {
      setSaving(false)
    }
  }

  function handleReset() {
    setResult(null)
    setInput('')
    setError('')
    setSuccess(false)
    inputRef.current?.focus()
  }

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900">NusaFinance</h1>
        <p className="text-sm text-gray-500">Catat pemasukan dan pengeluaran dengan AI</p>
      </div>

      <Card>
        <div className="flex items-center gap-2 mb-3">
          <Wallet className="w-5 h-5 text-[#0F9D8E]" />
          <CardTitle>Input Transaksi</CardTitle>
        </div>
        <div className="flex gap-2">
          <input
            ref={inputRef}
            value={input}
            onChange={e => setInput(e.target.value)}
            placeholder="Catat pemasukan atau pengeluaran... (contoh: jual beras 2kg 50rb)"
            className="input-field flex-1"
            onKeyDown={e => e.key === 'Enter' && handleParse()}
          />
          <Button onClick={handleParse} loading={loading}>Catat</Button>
        </div>
        {error && <p className="text-sm text-red-500 mt-2">{error}</p>}
      </Card>

      {loading && (
        <Card className="flex items-center justify-center gap-3 py-8">
          <LoadingSpinner />
          <span className="text-sm text-gray-500">AI sedang memproses...</span>
        </Card>
      )}

      {result && !loading && (
        <AiResultCard title="Hasil Parsing">
          <div className="space-y-3">
            <div className="flex items-center gap-2">
              <Badge variant={result.type === 'pemasukan' ? 'success' : 'danger'}>
                {result.type === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'}
              </Badge>
              {result.confidence >= 0.8 && <Badge variant="success">High Confidence</Badge>}
            </div>
            <div className="grid grid-cols-2 gap-3 text-sm">
              <div>
                <span className="text-gray-400">Item</span>
                <p className="font-medium text-gray-900">{result.item_name}</p>
              </div>
              <div>
                <span className="text-gray-400">Jumlah</span>
                <p className="font-medium text-gray-900">{formatCurrency(result.amount)}</p>
              </div>
              {result.quantity && (
                <div>
                  <span className="text-gray-400">Kuantitas</span>
                  <p className="font-medium text-gray-900">{result.quantity}</p>
                </div>
              )}
            </div>
            <div className="flex gap-2 pt-2">
              <Button onClick={handleSave} loading={saving}>
                <CheckCircle className="w-4 h-4" />
                Simpan
              </Button>
              <Button variant="outline" onClick={handleReset}>
                <RotateCcw className="w-4 h-4" />
                Ulangi
              </Button>
            </div>
          </div>
        </AiResultCard>
      )}

      {success && (
        <Card className="border-green-200 bg-green-50">
          <div className="flex items-center gap-3">
            <CheckCircle className="w-6 h-6 text-green-600" />
            <p className="text-sm font-medium text-green-800">Transaksi berhasil dicatat!</p>
          </div>
        </Card>
      )}

      <div>
        <h2 className="font-semibold text-gray-900 mb-3">Transaksi Sesi Ini</h2>
        {savedTransactions.length === 0 ? (
          <EmptyState
            icon={Wallet}
            title="Belum ada transaksi"
            description="Catat transaksi pertama Anda di atas"
          />
        ) : (
          <div className="space-y-2">
            {savedTransactions.map((tx) => (
              <Card key={tx.id} className="flex items-center gap-3 py-3 px-4">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${tx.type === 'pemasukan' ? 'bg-green-100' : 'bg-red-100'}`}>
                  {tx.type === 'pemasukan'
                    ? <ArrowDown className="w-5 h-5 text-green-600" />
                    : <ArrowUp className="w-5 h-5 text-red-600" />
                  }
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">{tx.item_name}</p>
                </div>
                <span className={`text-sm font-semibold ${tx.type === 'pemasukan' ? 'text-green-600' : 'text-red-600'}`}>
                  {tx.type === 'pemasukan' ? '+' : '-'}{formatCurrency(tx.amount)}
                </span>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
