'use client'
import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { History, ArrowDownCircle, ArrowUpCircle, Filter } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import type { StockMovement } from '@/types'

type MovementFilter = 'all' | 'in' | 'out' | 'adjustment'

const filterLabels: Record<MovementFilter, string> = {
  all: 'Semua',
  in: 'Masuk',
  out: 'Keluar',
  adjustment: 'Penyesuaian',
}

export default function StockMovementsPage() {
  const [movements, setMovements] = useState<StockMovement[]>([])
  const [loading, setLoading] = useState(true)
  const [filter, setFilter] = useState<MovementFilter>('all')

  const fetchMovements = async () => {
    setLoading(true)
    try {
      const res = await fetch('/api/data/stock-movements')
      const json = await res.json()
      setMovements(json.data || [])
    } catch {
      setMovements([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchMovements() }, [])

  const getTypeBadge = (type: string) => {
    switch (type) {
      case 'in':
        return { label: 'Masuk', variant: 'success' as const, icon: ArrowDownCircle }
      case 'out':
        return { label: 'Keluar', variant: 'danger' as const, icon: ArrowUpCircle }
      case 'adjustment':
        return { label: 'Penyesuaian', variant: 'info' as const, icon: Filter }
      default:
        return { label: type, variant: 'default' as const, icon: Filter }
    }
  }

  const filtered = filter === 'all' ? movements : movements.filter((m) => m.movement_type === filter)

  const filters: MovementFilter[] = ['all', 'in', 'out', 'adjustment']

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Riwayat Stok</h1>

      <div className="flex gap-1 bg-gray-100 rounded-xl p-1">
        {filters.map((f) => {
          const info = getTypeBadge(f)
          const Icon = f === 'all' ? History : info.icon
          return (
            <button
              key={f}
              onClick={() => setFilter(f)}
              className={`flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors ${
                filter === f ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'
              }`}
            >
              <Icon className="w-3.5 h-3.5" />
              {filterLabels[f]}
            </button>
          )
        })}
      </div>

      {filtered.length === 0 ? (
        <EmptyState
          icon={History}
          title="Belum ada riwayat"
          description="Pergerakan stok akan muncul di sini"
        />
      ) : (
        <div className="space-y-2">
          {filtered.map((m) => {
            const typeInfo = getTypeBadge(m.movement_type)
            const TypeIcon = typeInfo.icon
            return (
              <Card key={m.id}>
                <div className="flex items-start gap-3">
                  <div className={`p-2 rounded-lg ${
                    m.movement_type === 'in' ? 'bg-green-100' :
                    m.movement_type === 'out' ? 'bg-red-100' : 'bg-blue-100'
                  }`}>
                    <TypeIcon className={`w-4 h-4 ${
                      m.movement_type === 'in' ? 'text-green-600' :
                      m.movement_type === 'out' ? 'text-red-600' : 'text-blue-600'
                    }`} />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between">
                      <p className="font-medium text-sm text-gray-900">Produk #{m.product_id}</p>
                      <Badge variant={typeInfo.variant}>{typeInfo.label}</Badge>
                    </div>
                    <div className="flex items-center gap-3 mt-1 text-xs text-gray-500">
                      <span className="font-medium text-gray-700">{m.quantity > 0 ? '+' : ''}{m.quantity}</span>
                      {m.reason && <span className="truncate">{m.reason}</span>}
                    </div>
                    <p className="text-xs text-gray-400 mt-1">{formatDate(m.created_at)}</p>
                  </div>
                </div>
              </Card>
            )
          })}
        </div>
      )}
    </div>
  )
}
