'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { cn } from '@/lib/utils'
import { Home, Grid3x3, Wallet, Store, User } from 'lucide-react'

const navItems = [
  { label: 'Beranda', href: '/dashboard', icon: Home },
  { label: 'Fitur', href: '/dashboard/features', icon: Grid3x3 },
  { label: 'Transaksi', href: '/dashboard/transactions', icon: Wallet },
  { label: 'Usaha', href: '/dashboard/business', icon: Store },
  { label: 'Profil', href: '/dashboard/profile', icon: User },
]

export function BottomNav() {
  const pathname = usePathname()

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-100 shadow-lg pb-safe">
      <div className="flex items-center justify-around h-16 max-w-lg mx-auto px-2">
        {navItems.map((item) => {
          const isActive = pathname.startsWith(item.href)
          const Icon = item.icon
          return (
            <Link
              key={item.href}
              href={item.href}
              className={cn(
                'bottom-nav-item',
                isActive && 'active'
              )}
            >
              <Icon className={cn('w-5 h-5', isActive ? 'text-[#0F9D8E]' : 'text-gray-400')} />
              <span className={cn('text-[10px] font-medium', isActive ? 'text-[#0F9D8E]' : 'text-gray-400')}>
                {item.label}
              </span>
            </Link>
          )
        })}
      </div>
    </nav>
  )
}
