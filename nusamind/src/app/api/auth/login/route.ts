import { NextResponse } from 'next/server'
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'

export async function POST(req: Request) {
  const { email, password } = await req.json()
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
  
  const { data: { user } } = await supabase.auth.signInWithPassword({ email, password })
  if (!user) {
    return NextResponse.json({ error: 'Email atau password salah' }, { status: 401 })
  }

  // Get or create local user record
  let { data: profile } = await supabase
    .from('users')
    .select('*')
    .eq('id', user.id)
    .single()

  if (!profile) {
    const { data: newProfile } = await supabase
      .from('users')
      .insert({
        id: user.id,
        name: user.email?.split('@')[0] || 'User',
        email: user.email,
        role: 'user',
        status: 'active',
      })
      .select()
      .single()
    profile = newProfile
  }

  return NextResponse.json({ user: profile })
}
