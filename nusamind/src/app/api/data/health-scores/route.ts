import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { getAuthUser, getBusinessId } from '@/lib/supabase/helpers'

export async function GET(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const authUser = await getAuthUser(supabase)
    if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const businessId = await getBusinessId(supabase, authUser.email)
    if (!businessId) return NextResponse.json({ data: null })

    const { searchParams } = new URL(req.url)
    const latest = searchParams.get('latest')

    if (latest === 'true') {
      const { data, error } = await supabase
        .from('health_scores')
        .select('*')
        .eq('business_id', businessId)
        .order('scored_at', { ascending: false })
        .limit(1)
        .single()

      if (error && error.code !== 'PGRST116') return NextResponse.json({ error: error.message }, { status: 400 })
      return NextResponse.json({ data: data || null })
    }

    const { data, error } = await supabase
      .from('health_scores')
      .select('*')
      .eq('business_id', businessId)
      .order('scored_at', { ascending: false })

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
