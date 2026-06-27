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

    const body = await req.json()

    const response = await callGroq(SYSTEM_PROMPTS.finance, JSON.stringify(body))
    const data = await parseJsonResponse<{ type: string; item_name: string; amount: number; quantity: number; confidence: number }>(response)

    await supabase.from('ai_usage_logs').insert({
      user_id: authUser.localId,
      feature: 'finance',
      status: 'success',
    })

    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Gagal memproses permintaan' }, { status: 500 })
  }
}
