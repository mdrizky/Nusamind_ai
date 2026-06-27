'use client'

import Link from 'next/link'
import { Card } from '@/components/ui/Card'
import {
  Wallet, Image, BarChart3, MessageSquare, Package, Megaphone,
  Users, DollarSign, ShoppingBag, Globe, Activity, GraduationCap,
} from 'lucide-react'

interface Feature {
  icon: any
  title: string
  desc: string
  link: string
}

const features: Feature[] = [
  { icon: Wallet, title: 'NusaFinance', desc: 'Catat transaksi dari teks', link: '/finance' },
  { icon: Image, title: 'NusaMarketing', desc: 'Buat konten promosi', link: '/content' },
  { icon: BarChart3, title: 'NusaInsight', desc: 'Briefing bisnis AI', link: '/insight' },
  { icon: MessageSquare, title: 'NusaReply', desc: 'Balas chat pelanggan', link: '/reply' },
  { icon: Package, title: 'NusaStock', desc: 'Monitor stok barang', link: '/stock' },
  { icon: Megaphone, title: 'NusaCampaign', desc: 'Rencana promosi', link: '/campaign' },
  { icon: Users, title: 'NusaLoyal', desc: 'Manajemen pelanggan', link: '/loyal' },
  { icon: DollarSign, title: 'NusaPrice', desc: 'Analisis harga', link: '/price' },
  { icon: ShoppingBag, title: 'NusaCatalog', desc: 'Optimasi produk', link: '/catalog' },
  { icon: Globe, title: 'NusaGlobal', desc: 'Ekspor & terjemah', link: '/global' },
  { icon: Activity, title: 'NusaScore', desc: 'Skor kesehatan', link: '/score' },
  { icon: GraduationCap, title: 'NusaCoach', desc: 'Mentor bisnis AI', link: '/coach' },
]

export default function FeaturesPage() {
  return (
    <div className="p-4 space-y-4">
      <div>
        <h1 className="text-xl font-bold text-gray-900">Fitur Nusamind</h1>
        <p className="text-sm text-gray-500 mt-1">Pilih fitur AI untuk membantu bisnis Anda</p>
      </div>

      <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
        {features.map((feature) => {
          const Icon = feature.icon
          return (
            <Link key={feature.link} href={feature.link}>
              <Card className="flex flex-col items-center text-center gap-2 py-6 hover:shadow-md transition-shadow cursor-pointer h-full">
                <Icon className="w-8 h-8 text-[#0F9D8E]" />
                <h3 className="font-semibold text-sm text-gray-900">{feature.title}</h3>
                <p className="text-xs text-gray-500 leading-tight">{feature.desc}</p>
              </Card>
            </Link>
          )
        })}
      </div>
    </div>
  )
}
