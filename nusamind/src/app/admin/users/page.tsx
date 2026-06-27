import { createServerSupabase } from '@/lib/supabase/server'
import { formatDate } from '@/lib/utils'
import { Badge } from '@/components/ui/Badge'
import type { User } from '@/types'

async function getUsers() {
  const supabase = await createServerSupabase()
  const { data } = await supabase
    .from('users')
    .select('*')
    .order('created_at', { ascending: false })
  return data as User[] || []
}

export default async function AdminUsersPage() {
  const users = await getUsers()

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Users</h1>
        <span className="text-sm text-gray-500">{users.length} total</span>
      </div>

      <div className="card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                <th className="px-4 py-3 font-medium">Name</th>
                <th className="px-4 py-3 font-medium">Email</th>
                <th className="px-4 py-3 font-medium">Role</th>
                <th className="px-4 py-3 font-medium">Status</th>
                <th className="px-4 py-3 font-medium">Joined</th>
              </tr>
            </thead>
            <tbody>
              {users.map((user) => (
                <tr key={user.id} className="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-900 font-medium">{user.name}</td>
                  <td className="px-4 py-3 text-gray-500">{user.email}</td>
                  <td className="px-4 py-3">
                    <Badge variant={user.role === 'admin' ? 'info' : 'default'}>
                      {user.role}
                    </Badge>
                  </td>
                  <td className="px-4 py-3">
                    <Badge variant={user.status === 'active' ? 'success' : 'danger'}>
                      {user.status}
                    </Badge>
                  </td>
                  <td className="px-4 py-3 text-gray-500">{formatDate(user.created_at)}</td>
                </tr>
              ))}
              {users.length === 0 && (
                <tr>
                  <td colSpan={5} className="px-4 py-12 text-center text-gray-400">
                    No users found
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
