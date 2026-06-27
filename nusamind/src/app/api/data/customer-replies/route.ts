import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { getAuthUser, getBusinessId } from '@/lib/supabase/helpers'

export async function GET(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const authUser = await getAuthUser(supabase)
    if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const businessId = await getBusinessId(supabase, authUser.email)
    if (!businessId) return NextResponse.json({ data: [] })

    const { searchParams } = new URL(req.url)
    const isSaved = searchParams.get('is_saved')

    let query = supabase
      .from('customer_replies')
      .select('*')
      .eq('business_id', businessId)
      .order('created_at', { ascending: false })

    if (isSaved === 'true') query = query.eq('is_saved', true)
    else if (isSaved === 'false') query = query.eq('is_saved', false)

    const { data, error } = await query
    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function POST(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const authUser = await getAuthUser(supabase)
    if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const businessId = await getBusinessId(supabase, authUser.email)
    if (!businessId) return NextResponse.json({ error: 'Business not found' }, { status: 404 })

    const body = await req.json()
    const { data, error } = await supabase.from('customer_replies').insert({
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
    const authUser = await getAuthUser(supabase)
    if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const { searchParams } = new URL(req.url)
    const id = searchParams.get('id')
    if (!id) return NextResponse.json({ error: 'Reply ID required' }, { status: 400 })

    const { error } = await supabase.from('customer_replies').delete().eq('id', id)
    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ success: true })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
