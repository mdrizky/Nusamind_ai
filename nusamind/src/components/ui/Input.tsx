'use client'
import { cn } from '@/lib/utils'

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string
  error?: string
}

export function Input({ label, error, className, id, ...props }: InputProps) {
  return (
    <div className="space-y-1.5">
      {label && <label htmlFor={id} className="block text-sm font-medium text-gray-700">{label}</label>}
      <input id={id} className={cn('input-field', error && 'border-red-500', className)} {...props} />
      {error && <p className="text-xs text-red-500">{error}</p>}
    </div>
  )
}
