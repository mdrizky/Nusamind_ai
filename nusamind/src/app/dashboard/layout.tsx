import { redirect } from 'next/navigation'
import { createServerSupabase, getServerUser } from '@/lib/supabase/server'
import { BottomNav } from '@/components/layouts/bottom-nav'
import { Bell } from 'lucide-react'
import Link from 'next/link'

export default async function DashboardLayout({ children }: { children: React.ReactNode }) {
  const user = await getServerUser()
  if (!user) redirect('/login')
  if (user.role === 'admin') redirect('/admin')

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="gradient-header px-4 pt-4 pb-8 rounded-b-3xl">
        <div className="flex items-center justify-between mb-2">
          <div>
            <p className="text-sm text-white/80">Hai,</p>
            <h1 className="text-xl font-bold font-poppins">{user.name}</h1>
          </div>
          <Link href="/dashboard/notifications" className="p-2 rounded-full bg-white/20">
            <Bell className="w-5 h-5 text-white" />
          </Link>
        </div>
      </header>
      <main className="px-4 -mt-4 pb-24">
        {children}
      </main>
      <BottomNav />
    </div>
  )
}
