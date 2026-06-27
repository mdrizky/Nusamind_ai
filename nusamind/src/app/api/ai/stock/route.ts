import { NextResponse } from 'next/server'
import { callGroq, parseJsonResponse } from '@/lib/ai/groq'
import { SYSTEM_PROMPTS } from '@/lib/ai/prompts'
import { createServerSupabase } from '@/lib/supabase/server'
import { getAuthUser } from '@/lib/supabase/helpers'

export async function POST(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const authUser = await getAuthUser(supabase)
    if (!authUser) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const today = new Date().toISOString().split('T')[0]
    const { count } = await supabase
      .from('ai_usage_logs')
      .select('*', { count: 'exact', head: true })
      .eq('user_id', authUser.localId)
      .gte('created_at', today)

    if (count && count >= 30) {
      return NextResponse.json({ error: 'Batas AI harian tercapai (30/hari)' }, { status: 429 })
    }

    const { data: business } = await supabase
      .from('businesses')
      .select('id')
      .eq('user_id', authUser.localId)
      .single()

    if (!business) {
      return NextResponse.json({ error: 'Bisnis tidak ditemukan' }, { status: 404 })
    }

    const { data: products } = await supabase
      .from('products')
      .select('*')
      .eq('business_id', business.id)
      .lte('stock', supabase.rpc('get_min_stock_alert'))

    const stockData = {
      products: products || [],
      business_id: business.id,
    }

    const response = await callGroq(SYSTEM_PROMPTS.stock, JSON.stringify(stockData))
    const data = await parseJsonResponse<Array<{ product_name: string; status: string; current_stock: number; recommended_restock: number; reason: string }>>(response)

    await supabase.from('ai_usage_logs').insert({
      user_id: authUser.localId,
      feature: 'stock',
      status: 'success',
    })

    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Gagal memproses permintaan' }, { status: 500 })
  }
}
