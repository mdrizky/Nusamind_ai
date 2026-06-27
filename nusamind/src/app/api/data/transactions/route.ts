import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { getAuthUser } from '@/lib/supabase/helpers'

export async function GET(req: Request) {
  const supabase = await createServerSupabase()
  const authUser = await getAuthUser(supabase)
  if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

  const { searchParams } = new URL(req.url)
  const limit = parseInt(searchParams.get('limit') || '50')
  const page = parseInt(searchParams.get('page') || '1')
  const offset = (page - 1) * limit

  const { data, count } = await supabase
    .from('transactions')
    .select('*', { count: 'exact' })
    .eq('user_id', authUser.localId)
    .order('created_at', { ascending: false })
    .range(offset, offset + limit - 1)

  return NextResponse.json({ data, total: count, page, limit })
}

export async function POST(req: Request) {
  const supabase = await createServerSupabase()
  const authUser = await getAuthUser(supabase)
  if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

  const body = await req.json()
  const { data, error } = await supabase.from('transactions').insert({
    user_id: authUser.localId,
    ...body,
    transaction_date: body.transaction_date || new Date().toISOString().split('T')[0],
  }).select().single()

  if (error) return NextResponse.json({ error: error.message }, { status: 400 })
  return NextResponse.json({ data })
}
