import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { requireAdmin } from '@/lib/supabase/admin'

export async function GET() {
  try {
    const supabase = await createServerSupabase()
    const user = await requireAdmin()
    if (!user) return NextResponse.json({ error: 'Forbidden' }, { status: 403 })

    const { data, error } = await supabase
      .from('ai_usage_logs')
      .select('*, users(name)')
      .order('created_at', { ascending: false })

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
