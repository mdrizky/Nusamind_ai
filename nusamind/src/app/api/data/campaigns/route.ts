import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'

async function getUserId(supabase: any, email: string) {
  const { data } = await supabase.from('users').select('id').eq('email', email).single()
  return data?.id
}

async function getBusinessId(supabase: any, email: string) {
  const userId = await getUserId(supabase, email)
  if (!userId) return null
  const { data } = await supabase.from('businesses').select('id').eq('user_id', userId).single()
  return data?.id
}

export async function GET() {
  try {
    const supabase = await createServerSupabase()
    const { data: { user } } = await supabase.auth.getUser()
    if (!user) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const businessId = await getBusinessId(supabase, user.email!)
    if (!businessId) return NextResponse.json({ data: [] })

    const { data, error } = await supabase
      .from('campaign_plans')
      .select('*')
      .eq('business_id', businessId)
      .order('created_at', { ascending: false })

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function POST(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const { data: { user } } = await supabase.auth.getUser()
    if (!user) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const businessId = await getBusinessId(supabase, user.email!)
    if (!businessId) return NextResponse.json({ error: 'Business not found' }, { status: 404 })

    const body = await req.json()
    const { data, error } = await supabase.from('campaign_plans').insert({
      business_id: businessId,
      ...body,
    }).select().single()

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function DELETE(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const { data: { user } } = await supabase.auth.getUser()
    if (!user) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const { searchParams } = new URL(req.url)
    const id = searchParams.get('id')
    if (!id) return NextResponse.json({ error: 'Campaign ID required' }, { status: 400 })

    const { error } = await supabase.from('campaign_plans').delete().eq('id', id)
    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ success: true })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
