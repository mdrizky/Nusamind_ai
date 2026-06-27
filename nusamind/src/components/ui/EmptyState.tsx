import { Inbox } from 'lucide-react'

export function EmptyState({ icon: Icon = Inbox, title = 'Tidak ada data', description }: { icon?: any, title?: string, description?: string }) {
  return (
    <div className="flex flex-col items-center justify-center py-12 text-gray-400">
      <Icon className="w-12 h-12 mb-3" />
      <p className="font-medium text-gray-500">{title}</p>
      {description && <p className="text-sm mt-1">{description}</p>}
    </div>
  )
}
