import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'

export async function createServerSupabase() {
  const cookieStore = await cookies()
  return createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    {
      cookies: {
        getAll() {
          return cookieStore.getAll()
        },
        setAll(cookiesToSet) {
          cookiesToSet.forEach(({ name, value, options }) =>
            cookieStore.set(name, value, options)
          )
        },
      },
    }
  )
}

export async function getServerUser() {
  const supabase = await createServerSupabase()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return null

  // Try to find existing local user record
  const { data: existing } = await supabase
    .from('users')
    .select('*')
    .eq('id', user.id)
    .single()

  if (existing) return existing

  // First-time login: create local user record
  const { data: profile, error } = await supabase
    .from('users')
    .insert({
      id: user.id,
      name: user.user_metadata?.name || user.email?.split('@')[0] || 'User',
      email: user.email,
      role: 'user',
      status: 'active',
    })
    .select()
    .single()

  if (error) return null
  return profile
}
