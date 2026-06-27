'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { Cpu } from 'lucide-react'

interface AiUsageLog {
  id: number
  user_id: number
  feature: string
  tokens_used: number | null
  status: string
  created_at: string
  users: { name: string } | null
}

export default function AdminAiUsagePage() {
  const [logs, setLogs] = useState<AiUsageLog[]>([])
  const [loading, setLoading] = useState(true)
  const [featureFilter, setFeatureFilter] = useState('')

  useEffect(() => {
    fetch('/api/data/admin/ai-usage')
      .then(r => r.json())
      .then(data => setLogs(data.data || []))
      .finally(() => setLoading(false))
  }, [])

  const features = Array.from(new Set(logs.map(l => l.feature)))
  const filtered = featureFilter ? logs.filter(l => l.feature === featureFilter) : logs

  if (loading) return <PageLoading />

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">AI Usage Monitoring</h1>

      <div className="flex gap-2 items-center">
        <select
          className="input-field max-w-xs"
          value={featureFilter}
          onChange={e => setFeatureFilter(e.target.value)}
        >
          <option value="">Semua Fitur</option>
          {features.map(f => (
            <option key={f} value={f}>{f}</option>
          ))}
        </select>
        <span className="text-sm text-gray-500">{filtered.length} logs</span>
      </div>

      <Card className="overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-gray-500 border-b border-gray-100">
                <th className="p-4 font-medium">User</th>
                <th className="p-4 font-medium">Feature</th>
                <th className="p-4 font-medium">Date</th>
                <th className="p-4 font-medium">Tokens</th>
                <th className="p-4 font-medium">Status</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map(log => (
                <tr key={log.id} className="border-b border-gray-50 hover:bg-gray-50">
                  <td className="p-4 text-gray-900">{log.users?.name || 'Unknown'}</td>
                  <td className="p-4">
                    <div className="flex items-center gap-1.5">
                      <Cpu className="w-3.5 h-3.5 text-gray-400" />
                      <span className="text-gray-700">{log.feature}</span>
                    </div>
                  </td>
                  <td className="p-4 text-gray-500">{formatDate(log.created_at)}</td>
                  <td className="p-4 text-gray-700">{log.tokens_used ?? '-'}</td>
                  <td className="p-4">
                    <Badge variant={log.status === 'success' ? 'success' : log.status === 'error' ? 'danger' : 'warning'}>
                      {log.status}
                    </Badge>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {filtered.length === 0 && (
          <EmptyState title="No AI usage data" />
        )}
      </Card>
    </div>
  )
}
