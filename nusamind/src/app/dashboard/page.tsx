'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'
import { createClient } from '@/lib/supabase/client'
import { formatCurrency, formatDateShort } from '@/lib/utils'
import { Card } from '@/components/ui/Card'
import { LoadingSpinner, PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import {
  MessageSquare, Package, Megaphone, DollarSign, ShoppingBag,
  GraduationCap, TrendingUp, ArrowUp, ArrowDown, Wallet,
} from 'lucide-react'

interface Transaction {
  id: number
  type: 'pemasukan' | 'pengeluaran'
  item_name: string
  amount: number
  transaction_date: string
}

const quickActions = [
  { label: 'Balas Chat', href: '/reply', icon: MessageSquare },
  { label: 'Stok', href: '/stock', icon: Package },
  { label: 'Promo', href: '/campaign', icon: Megaphone },
  { label: 'Harga', href: '/price', icon: DollarSign },
  { label: 'Katalog', href: '/catalog', icon: ShoppingBag },
  { label: 'Mentor', href: '/coach', icon: GraduationCap },
]

export default function DashboardPage() {
  const supabase = createClient()
  const [userName, setUserName] = useState('')
  const [balance, setBalance] = useState<number | null>(null)
  const [transactions, setTransactions] = useState<Transaction[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    async function fetchData() {
      const { data: { user } } = await supabase.auth.getUser()
      if (user) {
        const { data: profile } = await supabase
          .from('users')
          .select('name')
          .eq('id', user.id)
          .single()
        if (profile) setUserName(profile.name)
      }

      const res = await fetch('/api/data/transactions?limit=5')
      if (res.ok) {
        const data = await res.json()
        setTransactions(data.data ?? data ?? [])
      }

      const revRes = await fetch('/api/data/transactions/summary')
      if (revRes.ok) {
        const summary = await revRes.json()
        const revenue = summary.total_revenue ?? 0
        const expense = summary.total_expense ?? 0
        setBalance(revenue - expense)
      }

      setLoading(false)
    }
    fetchData()
  }, [])

  if (loading) return <PageLoading />

  return (
    <div className="space-y-6 p-4">
      <div className="gradient-header -mx-4 -mt-4 px-4 pt-4 pb-6 rounded-b-3xl">
        <h1 className="text-2xl font-bold text-white">Hai, {userName || 'Pengguna'}!</h1>
        <p className="text-white/80 text-sm mt-1">Selamat datang di Nusamind</p>
      </div>

      <Card className="-mt-6 relative z-10">
        <div className="flex items-center gap-3">
          <div className="w-12 h-12 rounded-2xl bg-[#e8f5f3] flex items-center justify-center">
            <Wallet className="w-6 h-6 text-[#0F9D8E]" />
          </div>
          <div>
            <p className="text-sm text-gray-500">Saldo Kas</p>
            <p className="text-xl font-bold text-gray-900">
              {balance !== null ? formatCurrency(balance) : '-'}
            </p>
          </div>
        </div>
      </Card>

      <div>
        <h2 className="font-semibold text-gray-900 mb-3">Aksi Cepat</h2>
        <div className="grid grid-cols-3 gap-3">
          {quickActions.map((action) => {
            const Icon = action.icon
            return (
              <Link key={action.href} href={action.href}>
                <Card className="flex flex-col items-center gap-2 py-4 hover:shadow-md transition-shadow cursor-pointer">
                  <Icon className="w-6 h-6 text-[#0F9D8E]" />
                  <span className="text-xs text-gray-600 text-center font-medium">{action.label}</span>
                </Card>
              </Link>
            )
          })}
        </div>
      </div>

      <div>
        <div className="flex items-center justify-between mb-3">
          <h2 className="font-semibold text-gray-900">Transaksi Terakhir</h2>
          <Link href="/transactions" className="text-xs text-[#0F9D8E] font-medium">Lihat Semua</Link>
        </div>
        {transactions.length === 0 ? (
          <EmptyState title="Belum ada transaksi" description="Catat transaksi pertama Anda" />
        ) : (
          <div className="space-y-2">
            {transactions.map((tx) => (
              <Card key={tx.id} className="flex items-center gap-3 py-3 px-4">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${tx.type === 'pemasukan' ? 'bg-green-100' : 'bg-red-100'}`}>
                  {tx.type === 'pemasukan'
                    ? <ArrowDown className="w-5 h-5 text-green-600" />
                    : <ArrowUp className="w-5 h-5 text-red-600" />
                  }
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900 truncate">{tx.item_name}</p>
                  <p className="text-xs text-gray-400">{formatDateShort(tx.transaction_date)}</p>
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
