import { File, Image, FileText, Download, Trash2 } from 'lucide-react'
import { Button } from '@/components/ui/Button'
import { EmptyState } from '@/components/shared/EmptyState'
import { formatFileSize } from '@/lib/utils'
import { cn } from '@/lib/utils'
import type { TaskAttachment } from '../task.types'

interface TaskAttachmentListProps {
  attachments: TaskAttachment[]
  onDelete?: (id: number) => void
  canDelete?: boolean
  className?: string
}

function getFileIcon(fileType: string) {
  if (fileType.startsWith('image/')) return Image
  if (fileType.includes('pdf')) return FileText
  return File
}

export function TaskAttachmentList({ 
  attachments, 
  onDelete, 
  canDelete = false,
  className 
}: TaskAttachmentListProps) {
  if (attachments.length === 0) {
    return (
      <EmptyState
        icon={<File size={48} />}
        title="No attachments"
        description="No files have been uploaded yet"
        className="py-8"
      />
    )
  }
  
  return (
    <div className={cn("space-y-2", className)}>
      {attachments.map((attachment) => {
        const Icon = getFileIcon(attachment.file_type)
        
        return (
          <div
            key={attachment.id}
            className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
          >
            <div className="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
              <Icon size={20} className="text-gray-500" />
            </div>
            
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-gray-900 truncate">
                {attachment.file_name}
              </p>
              <p className="text-xs text-gray-500">
                {formatFileSize(attachment.file_size)}
              </p>
            </div>
            
            <div className="flex items-center gap-1">
              <Button
                variant="ghost"
                size="sm"
                onClick={() => {/* Download */}}
              >
                <Download size={16} />
              </Button>
              
              {canDelete && onDelete && (
                <Button
                  variant="ghost"
                  size="sm"
                  onClick={() => onDelete(attachment.id)}
                  className="text-danger hover:text-danger-dark"
                >
                  <Trash2 size={16} />
                </Button>
              )}
            </div>
          </div>
        )
      })}
    </div>
  )
}
