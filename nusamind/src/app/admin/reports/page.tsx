'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { Flag, CheckCircle } from 'lucide-react'

interface ContentReport {
  id: number
  user_id: number
  content_type: string
  content_id: number | null
  reason: string
  status: string
  created_at: string
  users: { name: string } | null
}

export default function AdminReportsPage() {
  const [reports, setReports] = useState<ContentReport[]>([])
  const [loading, setLoading] = useState(true)

  const fetchReports = async () => {
    const res = await fetch('/api/data/admin/reports')
    if (res.ok) {
      const data = await res.json()
      setReports(data.data || [])
    }
    setLoading(false)
  }

  useEffect(() => { fetchReports() }, [])

  const handleResolve = async (id: number) => {
    const res = await fetch('/api/data/admin/reports', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status: 'resolved' }),
    })
    if (res.ok) {
      setReports(prev =>
        prev.map(r => (r.id === id ? { ...r, status: 'resolved' } : r))
      )
    }
  }

  if (loading) return <PageLoading />

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Laporan Konten</h1>

      {reports.length === 0 ? (
        <Card>
          <EmptyState title="Tidak ada laporan" />
        </Card>
      ) : (
        <div className="space-y-3">
          {reports.map(report => (
            <Card key={report.id}>
              <div className="flex items-start gap-3">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${
                  report.status === 'resolved' ? 'bg-green-100' : 'bg-red-100'
                }`}>
                  <Flag className={`w-5 h-5 ${report.status === 'resolved' ? 'text-green-600' : 'text-red-600'}`} />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <p className="font-medium text-gray-900">{report.content_type}</p>
                    <Badge variant={report.status === 'resolved' ? 'success' : 'warning'}>
                      {report.status}
                    </Badge>
                  </div>
                  <p className="text-sm text-gray-600 mt-1">{report.reason}</p>
                  <div className="flex items-center gap-3 mt-2 text-xs text-gray-400">
                    <span>By: {report.users?.name || 'Unknown'}</span>
                    <span>{formatDate(report.created_at)}</span>
                  </div>
                </div>
                {report.status !== 'resolved' && (
                  <Button size="sm" variant="ghost" onClick={() => handleResolve(report.id)}>
                    <CheckCircle className="w-4 h-4 text-green-600" />
                  </Button>
                )}
              </div>
            </Card>
          ))}
        </div>
      )}
    </div>
  )
}
