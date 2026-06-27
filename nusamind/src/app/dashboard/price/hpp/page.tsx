'use client'

import { useState, useEffect } from 'react'
import { Card, CardHeader, CardTitle } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { PageLoading } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatCurrency, cn } from '@/lib/utils'
import type { Product } from '@/types'
import { Calculator, Info, ArrowRight, Package, DollarSign, Users, Factory } from 'lucide-react'

interface HppResult {
  hppPerUnit: number
  recommendedPrice: number
  totalCost: number
  profitPerUnit: number
  markup: number
}

export default function HppPage() {
  const [products, setProducts] = useState<Product[]>([])
  const [selectedId, setSelectedId] = useState('')
  const [loading, setLoading] = useState(true)
  const [bahanBaku, setBahanBaku] = useState('')
  const [tenagaKerja, setTenagaKerja] = useState('')
  const [overhead, setOverhead] = useState('')
  const [jumlahProduksi, setJumlahProduksi] = useState('')
  const [result, setResult] = useState<HppResult | null>(null)

  const selected = products.find((p) => p.id === Number(selectedId))

  useEffect(() => {
    fetch('/api/data/products')
      .then((r) => r.json())
      .then((data) => {
        if (Array.isArray(data)) setProducts(data)
        else if (data.data) setProducts(data.data)
      })
      .finally(() => setLoading(false))
  }, [])

  const calculate = () => {
    const bb = Number(bahanBaku) || 0
    const tk = Number(tenagaKerja) || 0
    const oh = Number(overhead) || 0
    const jp = Number(jumlahProduksi) || 1

    const totalCost = bb + tk + oh
    const hppPerUnit = totalCost / jp
    const markup = 1.3
    const recommendedPrice = hppPerUnit * markup
    const profitPerUnit = recommendedPrice - hppPerUnit

    setResult({ hppPerUnit, recommendedPrice, totalCost, profitPerUnit, markup })
  }

  if (loading) return <PageLoading />

  return (
    <div className="p-4 space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900 font-poppins">Kalkulator HPP</h1>
        <p className="text-sm text-gray-500 mt-0.5">Hitung Harga Pokok Penjualan</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Pilih Produk</CardTitle>
        </CardHeader>
        <select
          className="input-field"
          value={selectedId}
          onChange={(e) => { setSelectedId(e.target.value); setResult(null) }}
        >
          <option value="">-- Pilih Produk --</option>
          {products.map((p) => (
            <option key={p.id} value={p.id}>{p.name}</option>
          ))}
        </select>
      </Card>

      {products.length === 0 && (
        <EmptyState title="Belum ada produk" description="Tambahkan produk terlebih dahulu" />
      )}

      <Card>
        <CardHeader>
          <CardTitle>Biaya Produksi</CardTitle>
        </CardHeader>
        <div className="space-y-4">
          <Input
            label="Biaya Bahan Baku"
            id="bahan-baku"
            type="number"
            placeholder="Rp"
            value={bahanBaku}
            onChange={(e) => setBahanBaku(e.target.value)}
          />
          <Input
            label="Biaya Tenaga Kerja"
            id="tenaga-kerja"
            type="number"
            placeholder="Rp"
            value={tenagaKerja}
            onChange={(e) => setTenagaKerja(e.target.value)}
          />
          <Input
            label="Biaya Overhead"
            id="overhead"
            type="number"
            placeholder="Rp"
            value={overhead}
            onChange={(e) => setOverhead(e.target.value)}
          />
          <Input
            label="Jumlah Produksi"
            id="jumlah-produksi"
            type="number"
            placeholder="Unit"
            value={jumlahProduksi}
            onChange={(e) => setJumlahProduksi(e.target.value)}
          />
        </div>
      </Card>

      <Button className="w-full" disabled={!selected} onClick={calculate}>
        <Calculator className="w-4 h-4" />
        Hitung HPP
      </Button>

      {result && (
        <>
          <Card className="border-[#0F9D8E]/20 bg-[#e8f5f3]/30">
            <CardHeader>
              <CardTitle>Hasil Perhitungan</CardTitle>
            </CardHeader>
            <div className="space-y-4">
              <div className="text-center py-3">
                <p className="text-xs text-gray-500 uppercase tracking-wider">HPP per Unit</p>
                <p className="text-3xl font-bold text-[#0F9D8E] mt-1">{formatCurrency(result.hppPerUnit)}</p>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div className="bg-white rounded-xl p-3 border border-gray-100">
                  <p className="text-xs text-gray-500">Harga Jual Rekomendasi</p>
                  <p className="text-lg font-bold text-[#F2B705]">{formatCurrency(result.recommendedPrice)}</p>
                  <p className="text-[10px] text-gray-400">Markup {(result.markup - 1) * 100}%</p>
                </div>
                <div className="bg-white rounded-xl p-3 border border-gray-100">
                  <p className="text-xs text-gray-500">Laba per Unit</p>
                  <p className="text-lg font-bold text-green-600">{formatCurrency(result.profitPerUnit)}</p>
                </div>
              </div>
              <div className="bg-white rounded-xl p-3 border border-gray-100 flex items-center justify-between">
                <span className="text-sm text-gray-600">Total Biaya Produksi</span>
                <span className="font-bold text-gray-900">{formatCurrency(result.totalCost)}</span>
              </div>
            </div>
          </Card>

          <Card>
            <CardHeader>
              <div className="flex items-center gap-2">
                <Info className="w-4 h-4 text-[#0F9D8E]" />
                <CardTitle>Rumus Perhitungan</CardTitle>
              </div>
            </CardHeader>
            <div className="space-y-2 text-sm text-gray-600">
              <p><strong>HPP per Unit</strong> = (Bahan Baku + Tenaga Kerja + Overhead) ÷ Jumlah Produksi</p>
              <p><strong>Harga Jual</strong> = HPP per Unit × 1.3 (Markup 30%)</p>
              <p><strong>Laba per Unit</strong> = Harga Jual − HPP per Unit</p>
            </div>
          </Card>
        </>
      )}
    </div>
  )
}
