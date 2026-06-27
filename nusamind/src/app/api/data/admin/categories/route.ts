import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { requireAdmin } from '@/lib/supabase/admin'

export async function GET() {
  try {
    const supabase = await createServerSupabase()
    const user = await requireAdmin()
    if (!user) return NextResponse.json({ error: 'Forbidden' }, { status: 403 })

    const { data, error } = await supabase
      .from('categories')
      .select('*')
      .order('name')

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function POST(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const user = await requireAdmin()
    if (!user) return NextResponse.json({ error: 'Forbidden' }, { status: 403 })

    const body = await req.json()
    const { data, error } = await supabase.from('categories').insert(body).select().single()

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function PUT(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const user = await requireAdmin()
    if (!user) return NextResponse.json({ error: 'Forbidden' }, { status: 403 })

    const body = await req.json()
    const { id, ...updates } = body
    if (!id) return NextResponse.json({ error: 'Category ID required' }, { status: 400 })

    const { data, error } = await supabase
      .from('categories')
      .update(updates)
      .eq('id', id)
      .select()
      .single()

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ data })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}

export async function DELETE(req: Request) {
  try {
    const supabase = await createServerSupabase()
    const user = await requireAdmin()
    if (!user) return NextResponse.json({ error: 'Forbidden' }, { status: 403 })

    const { searchParams } = new URL(req.url)
    const id = searchParams.get('id')
    if (!id) return NextResponse.json({ error: 'Category ID required' }, { status: 400 })

    const { error } = await supabase.from('categories').delete().eq('id', id)
    if (error) return NextResponse.json({ error: error.message }, { status: 400 })
    return NextResponse.json({ success: true })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
