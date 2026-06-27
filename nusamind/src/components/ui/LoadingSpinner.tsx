import { Loader2 } from 'lucide-react'
import { cn } from '@/lib/utils'

export function LoadingSpinner({ className }: { className?: string }) {
  return <Loader2 className={cn('w-6 h-6 animate-spin text-[#0F9D8E]', className)} />
}

export function PageLoading() {
  return (
    <div className="flex items-center justify-center min-h-[60vh]">
      <LoadingSpinner className="w-8 h-8" />
    </div>
  )
}
