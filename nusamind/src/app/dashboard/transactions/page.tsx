'use client'

import { useState, useEffect } from 'react'
import { Card } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { Modal } from '@/components/ui/Modal'
import { Input } from '@/components/ui/Input'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatCurrency, formatDate } from '@/lib/utils'
import { ArrowDown, ArrowUp, Plus, Wallet } from 'lucide-react'

interface Transaction {
  id: number
  type: 'pemasukan' | 'pengeluaran'
  item_name: string
  amount: number
  source: string
  transaction_date: string
}

const tabs = [
  { key: 'all', label: 'Semua' },
  { key: 'pemasukan', label: 'Pemasukan' },
  { key: 'pengeluaran', label: 'Pengeluaran' },
]

export default function TransactionsPage() {
  const [transactions, setTransactions] = useState<Transaction[]>([])
  const [loading, setLoading] = useState(true)
  const [activeTab, setActiveTab] = useState('all')
  const [page, setPage] = useState(1)
  const [hasMore, setHasMore] = useState(true)
  const [modalOpen, setModalOpen] = useState(false)
  const [saving, setSaving] = useState(false)
  const [form, setForm] = useState({ type: 'pemasukan', item_name: '', amount: '', source: 'manual', transaction_date: '' })

  const fetchTransactions = async (pageNum: number, append = false) => {
    const res = await fetch(`/api/data/transactions?limit=20&page=${pageNum}`)
    if (res.ok) {
      const data = await res.json()
      const items = data.data || []
      if (append) {
        setTransactions(prev => [...prev, ...items])
      } else {
        setTransactions(items)
      }
      setHasMore(items.length === 20)
    }
  }

  useEffect(() => {
    setLoading(true)
    setPage(1)
    fetchTransactions(1).finally(() => setLoading(false))
  }, [])

  const loadMore = () => {
    const nextPage = page + 1
    setPage(nextPage)
    fetchTransactions(nextPage, true)
  }

  const filtered = activeTab === 'all'
    ? transactions
    : transactions.filter(t => t.type === activeTab)

  const totalIncome = transactions.filter(t => t.type === 'pemasukan').reduce((s, t) => s + t.amount, 0)
  const totalExpense = transactions.filter(t => t.type === 'pengeluaran').reduce((s, t) => s + t.amount, 0)
  const balance = totalIncome - totalExpense

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setSaving(true)
    try {
      const res = await fetch('/api/data/transactions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...form,
          amount: parseInt(form.amount),
          transaction_date: form.transaction_date || new Date().toISOString().split('T')[0],
        }),
      })
      if (res.ok) {
        setModalOpen(false)
        setForm({ type: 'pemasukan', item_name: '', amount: '', source: 'manual', transaction_date: '' })
        fetchTransactions(1)
      }
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <PageLoading />

  const sourceColors: Record<string, string> = {
    manual: 'default',
    omnichannel: 'info',
    import: 'warning',
  }

  return (
    <div className="p-4 space-y-5">
      <h1 className="text-xl font-bold text-gray-900 font-poppins">Transaksi</h1>

      <Card>
        <div className="flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-[#e8f5f3] flex items-center justify-center">
            <Wallet className="w-6 h-6 text-[#0F9D8E]" />
          </div>
          <div className="flex-1">
            <p className="text-sm text-gray-500">Saldo Kas</p>
            <p className="text-xl font-bold text-gray-900">{formatCurrency(balance)}</p>
          </div>
        </div>
        <div className="flex gap-4 mt-3 pt-3 border-t border-gray-100">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
              <ArrowDown className="w-4 h-4 text-green-600" />
            </div>
            <div>
              <p className="text-xs text-gray-400">Pemasukan</p>
              <p className="text-sm font-semibold text-green-600">{formatCurrency(totalIncome)}</p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
              <ArrowUp className="w-4 h-4 text-red-600" />
            </div>
            <div>
              <p className="text-xs text-gray-400">Pengeluaran</p>
              <p className="text-sm font-semibold text-red-600">{formatCurrency(totalExpense)}</p>
            </div>
          </div>
        </div>
      </Card>

      <div className="flex gap-2 overflow-x-auto">
        {tabs.map(tab => (
          <button
            key={tab.key}
            onClick={() => setActiveTab(tab.key)}
            className={`px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-colors ${
              activeTab === tab.key
                ? 'bg-[#0F9D8E] text-white'
                : 'bg-white text-gray-600 border border-gray-200'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {filtered.length === 0 ? (
        <EmptyState title="Belum ada transaksi" description="Catat transaksi pertama Anda" />
      ) : (
        <div className="space-y-2">
          {filtered.map(tx => (
            <Card key={tx.id} className="flex items-center gap-3 py-3 px-4">
              <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${tx.type === 'pemasukan' ? 'bg-green-100' : 'bg-red-100'}`}>
                {tx.type === 'pemasukan'
                  ? <ArrowDown className="w-5 h-5 text-green-600" />
                  : <ArrowUp className="w-5 h-5 text-red-600" />
                }
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-900 truncate">{tx.item_name}</p>
                <div className="flex items-center gap-2 mt-0.5">
                  <span className="text-xs text-gray-400">{formatDate(tx.transaction_date)}</span>
                  <Badge variant={(sourceColors[tx.source] || 'default') as any}>{tx.source}</Badge>
                </div>
              </div>
              <span className={`text-sm font-semibold ${tx.type === 'pemasukan' ? 'text-green-600' : 'text-red-600'}`}>
                {tx.type === 'pemasukan' ? '+' : '-'}{formatCurrency(tx.amount)}
              </span>
            </Card>
          ))}
        </div>
      )}

      {hasMore && filtered.length > 0 && (
        <Button variant="outline" className="w-full" onClick={loadMore}>
          Muat Lebih Banyak
        </Button>
      )}

      <button
        onClick={() => setModalOpen(true)}
        className="fixed bottom-24 right-4 w-14 h-14 bg-[#0F9D8E] text-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#0c8578] transition-colors z-40"
      >
        <Plus className="w-6 h-6" />
      </button>

      <Modal open={modalOpen} onClose={() => setModalOpen(false)} title="Catat Transaksi">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="flex gap-2">
            <button
              type="button"
              onClick={() => setForm(f => ({ ...f, type: 'pemasukan' }))}
              className={`flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors ${
                form.type === 'pemasukan'
                  ? 'bg-green-100 text-green-700 border-2 border-green-500'
                  : 'bg-gray-50 text-gray-500 border-2 border-transparent'
              }`}
            >
              <ArrowDown className="w-4 h-4 inline mr-1" />
              Pemasukan
            </button>
            <button
              type="button"
              onClick={() => setForm(f => ({ ...f, type: 'pengeluaran' }))}
              className={`flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors ${
                form.type === 'pengeluaran'
                  ? 'bg-red-100 text-red-700 border-2 border-red-500'
                  : 'bg-gray-50 text-gray-500 border-2 border-transparent'
              }`}
            >
              <ArrowUp className="w-4 h-4 inline mr-1" />
              Pengeluaran
            </button>
          </div>
          <Input
            label="Nama Item"
            value={form.item_name}
            onChange={e => setForm(f => ({ ...f, item_name: e.target.value }))}
            required
          />
          <Input
            label="Jumlah (Rp)"
            type="number"
            value={form.amount}
            onChange={e => setForm(f => ({ ...f, amount: e.target.value }))}
            required
          />
          <Input
            label="Tanggal"
            type="date"
            value={form.transaction_date}
            onChange={e => setForm(f => ({ ...f, transaction_date: e.target.value }))}
          />
          <Button type="submit" className="w-full" loading={saving}>
            Simpan
          </Button>
        </form>
      </Modal>
    </div>
  )
}
