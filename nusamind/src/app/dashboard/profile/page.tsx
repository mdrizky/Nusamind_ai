'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { Input } from '@/components/ui/Input'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { User, Mail, Shield, Cpu, LogOut } from 'lucide-react'
import { useRouter } from 'next/navigation'

export default function ProfilePage() {
  const router = useRouter()
  const [user, setUser] = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const [name, setName] = useState('')
  const [saving, setSaving] = useState(false)
  const [aiUsage, setAiUsage] = useState(0)
  const DAILY_LIMIT = 30

  useEffect(() => {
    async function loadProfile() {
      const supabase = (await import('@/lib/supabase/client')).createClient()
      const { data: { user: authUser } } = await supabase.auth.getUser()
      if (authUser) {
        const { data: profile } = await supabase
          .from('users')
          .select('*')
          .eq('id', authUser.id)
          .single()
        if (profile) {
          setUser(profile)
          setName(profile.name || '')
        }
      }

      const res = await fetch('/api/data/content-generations')
      if (res.ok) {
        const data = await res.json()
        const today = new Date().toISOString().split('T')[0]
        const todayCount = (data.data || []).filter(
          (g: any) => g.created_at?.startsWith(today)
        ).length
        setAiUsage(todayCount)
      }

      setLoading(false)
    }
    loadProfile()
  }, [])

  const handleSaveName = async () => {
    setSaving(true)
    try {
      const supabase = (await import('@/lib/supabase/client')).createClient()
      await supabase.from('users').update({ name }).eq('id', user.id)
      setUser((prev: any) => ({ ...prev, name }))
    } finally {
      setSaving(false)
    }
  }

  const handleLogout = async () => {
    await fetch('/api/auth/logout', { method: 'POST' })
    const supabase = (await import('@/lib/supabase/client')).createClient()
    await supabase.auth.signOut()
    router.push('/login')
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <h1 className="text-xl font-bold text-gray-900 font-poppins">Profil</h1>

      <Card>
        <div className="flex items-center gap-4">
          <div className="w-16 h-16 rounded-2xl bg-[#e8f5f3] flex items-center justify-center">
            <span className="text-2xl font-bold text-[#0F9D8E]">
              {user?.name?.charAt(0)?.toUpperCase() || 'U'}
            </span>
          </div>
          <div className="flex-1">
            <h2 className="font-semibold text-gray-900 text-lg">{user?.name || 'Pengguna'}</h2>
            <div className="flex items-center gap-2 mt-1">
              <Mail className="w-3.5 h-3.5 text-gray-400" />
              <span className="text-sm text-gray-500">{user?.email}</span>
            </div>
            <div className="mt-1">
              <Badge variant={user?.role === 'admin' ? 'info' : 'default'}>
                <Shield className="w-3 h-3 inline mr-1" />
                {user?.role || 'user'}
              </Badge>
            </div>
          </div>
        </div>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Ubah Nama</CardTitle>
        </CardHeader>
        <div className="flex gap-2">
          <Input value={name} onChange={e => setName(e.target.value)} placeholder="Nama Anda" />
          <Button onClick={handleSaveName} loading={saving} disabled={name === user?.name}>
            Simpan
          </Button>
        </div>
      </Card>

      <Card>
        <CardHeader>
          <div className="flex items-center gap-2">
            <Cpu className="w-5 h-5 text-[#0F9D8E]" />
            <CardTitle>AI Usage Hari Ini</CardTitle>
          </div>
        </CardHeader>
        <div className="space-y-3">
          <div className="flex items-center justify-between">
            <span className="text-sm text-gray-600">Used {aiUsage} of {DAILY_LIMIT} today</span>
            <span className={`text-sm font-semibold ${aiUsage >= DAILY_LIMIT ? 'text-red-600' : 'text-green-600'}`}>
              {DAILY_LIMIT - aiUsage} remaining
            </span>
          </div>
          <div className="w-full bg-gray-100 rounded-full h-2.5">
            <div
              className={`h-2.5 rounded-full transition-all ${aiUsage >= DAILY_LIMIT ? 'bg-red-500' : 'bg-[#0F9D8E]'}`}
              style={{ width: `${Math.min(100, (aiUsage / DAILY_LIMIT) * 100)}%` }}
            />
          </div>
        </div>
      </Card>

      <Button variant="outline" className="w-full text-red-500 border-red-200 hover:bg-red-50 hover:text-red-600" onClick={handleLogout}>
        <LogOut className="w-4 h-4" />
        Logout
      </Button>
    </div>
  )
}
