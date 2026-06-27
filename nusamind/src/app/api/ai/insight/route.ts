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

    const sevenDaysAgo = new Date()
    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7)

    const { data: transactions, error: txError } = await supabase
      .from('transactions')
      .select('*')
      .eq('user_id', authUser.localId)
      .gte('created_at', sevenDaysAgo.toISOString())
      .order('created_at', { ascending: false })

    if (txError) {
      return NextResponse.json({ error: 'Gagal mengambil data transaksi' }, { status: 500 })
    }

    const summary = {
      total_transactions: transactions?.length || 0,
      total_revenue: transactions?.reduce((sum: number, tx: any) => sum + Number(tx.total_amount || 0), 0) || 0,
      period: '7 hari terakhir',
    }

    const response = await callGroq(SYSTEM_PROMPTS.insight, JSON.stringify(summary))
    const data = await parseJsonResponse<{ narrative: string; summary: Record<string, any>; suggestions: string[] }>(response)

    await supabase.from('ai_usage_logs').insert({
      user_id: authUser.localId,
      feature: 'insight',
      status: 'success',
    })

    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Gagal memproses permintaan' }, { status: 500 })
  }
}
