import type { SupabaseClient } from '@supabase/supabase-js'

export type AuthUser = {
  authId: string
  email: string
  localId: string
}

export async function getLocalUserId(supabase: SupabaseClient, email: string): Promise<string | null> {
  const { data } = await supabase.from('users').select('id').eq('email', email).single()
  return data?.id ?? null
}

export async function getBusinessId(supabase: SupabaseClient, email: string): Promise<number | null> {
  const userId = await getLocalUserId(supabase, email)
  if (!userId) return null
  const { data } = await supabase.from('businesses').select('id').eq('user_id', userId).single()
  return data?.id ?? null
}

export async function getAuthUser(supabase: SupabaseClient): Promise<AuthUser | null> {
  const { data: { user } } = await supabase.auth.getUser()
  if (!user?.email) return null
  const localId = await getLocalUserId(supabase, user.email)
  if (!localId) return null
  return { authId: user.id, email: user.email, localId }
}
