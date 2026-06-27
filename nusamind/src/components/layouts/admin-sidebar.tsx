'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { cn } from '@/lib/utils'

const navLinks = [
  { label: 'Dashboard', href: '/admin' },
  { label: 'Users', href: '/admin/users' },
  { label: 'AI Usage', href: '/admin/ai-usage' },
  { label: 'Categories', href: '/admin/categories' },
  { label: 'Reports', href: '/admin/reports' },
]

export function AdminSidebar() {
  const pathname = usePathname()

  return (
    <aside className="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-gray-200">
      <div className="flex items-center h-16 px-6 border-b border-gray-100">
        <Link href="/admin" className="text-lg font-bold text-[#0F9D8E] font-poppins">
          Nusamind Admin
        </Link>
      </div>
      <nav className="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        {navLinks.map((link) => {
          const isActive = pathname === link.href || (link.href !== '/admin' && pathname.startsWith(link.href))
          return (
            <Link
              key={link.href}
              href={link.href}
              className={cn(
                'flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-colors duration-200',
                isActive
                  ? 'bg-[#0F9D8E]/10 text-[#0F9D8E]'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              )}
            >
              {link.label}
            </Link>
          )
        })}
      </nav>
    </aside>
  )
}
