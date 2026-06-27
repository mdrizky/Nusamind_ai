import { redirect } from 'next/navigation'
import { getServerUser } from '@/lib/supabase/server'
import { AdminSidebar } from '@/components/layouts/admin-sidebar'
import Link from 'next/link'

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const user = await getServerUser()
  if (!user) redirect('/login')
  if (user.role !== 'admin') redirect('/dashboard')

  return (
    <div className="min-h-screen bg-gray-50">
      <AdminSidebar />

      <div className="lg:pl-64">
        <header className="sticky top-0 z-40 bg-white border-b border-gray-200">
          <div className="flex items-center justify-end h-16 px-6">
            <div className="flex items-center gap-3">
              <button className="lg:hidden p-2 rounded-lg hover:bg-gray-100" type="button">
                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
              <Link href="/profile" className="text-sm font-medium text-gray-700 hover:text-gray-900">
                {user.name}
              </Link>
              <div className="w-8 h-8 rounded-full bg-[#0F9D8E]/10 flex items-center justify-center">
                <span className="text-xs font-semibold text-[#0F9D8E]">
                  {user.name.charAt(0).toUpperCase()}
                </span>
              </div>
            </div>
          </div>
        </header>

        <main className="p-6">
          {children}
        </main>
      </div>
    </div>
  )
}
