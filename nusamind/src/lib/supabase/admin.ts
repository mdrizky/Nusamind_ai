import { createServerSupabase } from './server'

export async function requireAdmin() {
  const supabase = await createServerSupabase()
  const { data: { user: authUser } } = await supabase.auth.getUser()
  if (!authUser) return null

  const { data: profile } = await supabase
    .from('users')
    .select('role')
    .eq('id', authUser.id)
    .single()

  if (!profile || profile.role !== 'admin') return null
  return authUser
}
