'use client'

import { useState, useEffect } from 'react'
import { Card } from '@/components/ui/Card'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { Bell, BellOff } from 'lucide-react'
import type { Notification } from '@/types'

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [loading, setLoading] = useState(true)

  const fetchNotifications = async () => {
    const supabase = (await import('@/lib/supabase/client')).createClient()
    const { data: { user } } = await supabase.auth.getUser()
    if (!user) return

    const { data } = await supabase
      .from('notifications')
      .select('*')
      .eq('user_id', user.id)
      .order('created_at', { ascending: false })

    setNotifications(data || [])
    setLoading(false)
  }

  useEffect(() => {
    fetchNotifications()
  }, [])

  const markAsRead = async (id: number) => {
    const supabase = (await import('@/lib/supabase/client')).createClient()
    await supabase.from('notifications').update({ is_read: true }).eq('id', id)
    setNotifications(prev =>
      prev.map(n => (n.id === id ? { ...n, is_read: true } : n))
    )
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <h1 className="text-xl font-bold text-gray-900 font-poppins">Notifikasi</h1>

      {notifications.length === 0 ? (
        <EmptyState icon={BellOff} title="Tidak ada notifikasi" description="Semua sudah dibaca" />
      ) : (
        <div className="space-y-2">
          {notifications.map(notification => (
            <Card
              key={notification.id}
              className={`flex items-start gap-3 py-3 px-4 cursor-pointer transition-colors hover:bg-gray-50 ${!notification.is_read ? 'border-l-4 border-l-[#0F9D8E]' : ''}`}
              onClick={() => !notification.is_read && markAsRead(notification.id)}
            >
              <div className={`w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${notification.is_read ? 'bg-gray-100' : 'bg-[#e8f5f3]'}`}>
                <Bell className={`w-5 h-5 ${notification.is_read ? 'text-gray-400' : 'text-[#0F9D8E]'}`} />
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2">
                  <p className={`text-sm ${notification.is_read ? 'text-gray-500' : 'text-gray-900 font-medium'}`}>
                    {notification.title}
                  </p>
                  {!notification.is_read && (
                    <span className="w-2 h-2 rounded-full bg-[#0F9D8E] shrink-0" />
                  )}
                </div>
                <p className="text-xs text-gray-400 mt-0.5 line-clamp-2">{notification.body}</p>
                <p className="text-xs text-gray-400 mt-1">{formatDate(notification.created_at)}</p>
              </div>
            </Card>
          ))}
        </div>
      )}
    </div>
  )
}
