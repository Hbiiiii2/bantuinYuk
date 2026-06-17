import { cn } from '@/lib/utils'

interface FilterOption {
  value: string
  label: string
}

interface FilterBarProps {
  options: FilterOption[]
  selectedValue: string
  onChange: (value: string) => void
  label?: string
}

export function FilterBar({ options, selectedValue, onChange, label }: FilterBarProps) {
  return (
    <div className="flex flex-wrap items-center gap-2">
      {label && <span className="text-sm font-medium text-gray-500 mr-2">{label}:</span>}
      <div className="flex bg-gray-100 rounded-lg p-1">
        {options.map((opt) => {
          const isActive = selectedValue === opt.value
          return (
            <button
              key={opt.value}
              onClick={() => onChange(opt.value)}
              className={cn(
                "px-3 py-1.5 rounded-md text-xs font-semibold transition-all min-h-[36px]",
                isActive
                  ? "bg-white text-gray-900 shadow-sm"
                  : "text-gray-500 hover:text-gray-700"
              )}
            >
              {opt.label}
            </button>
          )
        })}
      </div>
    </div>
  )
}
