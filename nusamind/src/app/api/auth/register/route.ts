import { NextResponse } from 'next/server'
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'

export async function POST(req: Request) {
  const { email, password, name } = await req.json()
  const supabase = createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    {
      cookies: {
        getAll: () => [],
        setAll: () => {},
      },
    }
  )

  const { data: { user } } = await supabase.auth.signUp({ email, password })
  if (!user) {
    return NextResponse.json({ error: 'Gagal mendaftar' }, { status: 400 })
  }

  const { error } = await supabase.from('users').insert({
    id: user.id,
    name,
    email,
    role: 'user',
    status: 'active',
  })

  if (error) {
    return NextResponse.json({ error: error.message }, { status: 400 })
  }

  return NextResponse.json({ success: true })
}
