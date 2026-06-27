import { createServerSupabase } from '@/lib/supabase/server'
import { formatDate } from '@/lib/utils'
import { Badge } from '@/components/ui/Badge'

async function getStats() {
  const supabase = await createServerSupabase()

  const { count: totalUsers } = await supabase
    .from('users')
    .select('*', { count: 'exact', head: true })

  const { count: activeBusinesses } = await supabase
    .from('businesses')
    .select('*', { count: 'exact', head: true })

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const { count: aiUsageToday } = await supabase
    .from('ai_usage_logs')
    .select('*', { count: 'exact', head: true })
    .gte('created_at', today.toISOString())

  const { count: totalReports } = await supabase
    .from('business_insights')
    .select('*', { count: 'exact', head: true })

  return { totalUsers, activeBusinesses, aiUsageToday, totalReports }
}

async function getRecentUsers() {
  const supabase = await createServerSupabase()
  const { data } = await supabase
    .from('users')
    .select('*')
    .order('created_at', { ascending: false })
    .limit(5)
  return data || []
}

async function getRecentAiUsage() {
  const supabase = await createServerSupabase()
  const { data } = await supabase
    .from('ai_usage_logs')
    .select('*, users(name)')
    .order('created_at', { ascending: false })
    .limit(10)
  return data || []
}

export default async function AdminDashboardPage() {
  const stats = await getStats()
  const recentUsers = await getRecentUsers()
  const recentAiUsage = await getRecentAiUsage()

  const statCards = [
    { label: 'Total Users', value: stats.totalUsers ?? 0, color: 'bg-blue-500' },
    { label: 'Active Businesses', value: stats.activeBusinesses ?? 0, color: 'bg-green-500' },
    { label: 'AI Usage Today', value: stats.aiUsageToday ?? 0, color: 'bg-purple-500' },
    { label: 'Reports', value: stats.totalReports ?? 0, color: 'bg-orange-500' },
  ]

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {statCards.map((stat) => (
          <div key={stat.label} className="card flex items-center gap-4">
            <div className={`w-12 h-12 rounded-xl ${stat.color} flex items-center justify-center`}>
              <span className="text-white text-lg font-bold">{stat.value}</span>
            </div>
            <div>
              <p className="text-sm text-gray-500">{stat.label}</p>
              <p className="text-xl font-bold text-gray-900">{stat.value}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="card">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Recent Users</h2>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="text-left text-gray-500 border-b border-gray-100">
                  <th className="pb-2 font-medium">Name</th>
                  <th className="pb-2 font-medium">Email</th>
                  <th className="pb-2 font-medium">Role</th>
                  <th className="pb-2 font-medium">Status</th>
                  <th className="pb-2 font-medium">Joined</th>
                </tr>
              </thead>
              <tbody>
                {recentUsers.map((user) => (
                  <tr key={user.id} className="border-b border-gray-50">
                    <td className="py-2.5 text-gray-900">{user.name}</td>
                    <td className="py-2.5 text-gray-500">{user.email}</td>
                    <td className="py-2.5">
                      <Badge variant={user.role === 'admin' ? 'info' : 'default'}>
                        {user.role}
                      </Badge>
                    </td>
                    <td className="py-2.5">
                      <Badge variant={user.status === 'active' ? 'success' : 'danger'}>
                        {user.status}
                      </Badge>
                    </td>
                    <td className="py-2.5 text-gray-500">{formatDate(user.created_at)}</td>
                  </tr>
                ))}
                {recentUsers.length === 0 && (
                  <tr>
                    <td colSpan={5} className="py-8 text-center text-gray-400">No users yet</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        <div className="card">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Recent AI Usage</h2>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="text-left text-gray-500 border-b border-gray-100">
                  <th className="pb-2 font-medium">User</th>
                  <th className="pb-2 font-medium">Feature</th>
                  <th className="pb-2 font-medium">Status</th>
                  <th className="pb-2 font-medium">Time</th>
                </tr>
              </thead>
              <tbody>
                {recentAiUsage.map((log) => (
                  <tr key={log.id} className="border-b border-gray-50">
                    <td className="py-2.5 text-gray-900">
                      {(log as any).users?.name || 'Unknown'}
                    </td>
                    <td className="py-2.5 text-gray-500">{log.feature}</td>
                    <td className="py-2.5">
                      <Badge variant={log.status === 'success' ? 'success' : log.status === 'error' ? 'danger' : 'warning'}>
                        {log.status}
                      </Badge>
                    </td>
                    <td className="py-2.5 text-gray-500">{formatDate(log.created_at)}</td>
                  </tr>
                ))}
                {recentAiUsage.length === 0 && (
                  <tr>
                    <td colSpan={4} className="py-8 text-center text-gray-400">No AI usage yet</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  )
}
