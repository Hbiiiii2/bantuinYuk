import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Upload, X } from 'lucide-react'
import { Textarea } from '@/components/ui/Textarea'
import { Button } from '@/components/ui/Button'
import { Card, CardContent } from '@/components/ui/Card'
import { useCreateProgress } from '../hooks'

const progressSchema = z.object({
  description: z.string().min(10, 'Description must be at least 10 characters')
})

type ProgressFormData = z.infer<typeof progressSchema>

interface ProgressFormProps {
  taskId: number
  onSuccess?: () => void
}

export function ProgressForm({ taskId, onSuccess }: ProgressFormProps) {
  const [files, setFiles] = useState<File[]>([])
  const createProgress = useCreateProgress()
  
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors }
  } = useForm<ProgressFormData>({
    resolver: zodResolver(progressSchema)
  })
  
  const onSubmit = async (data: ProgressFormData) => {
    try {
      await createProgress.mutateAsync({
        taskId,
        data: {
          description: data.description,
          attachment_ids: []
        }
      })
      reset()
      setFiles([])
      onSuccess?.()
    } catch (error) {
      // Error handled by mutation
    }
  }
  
  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFiles = Array.from(e.target.files || [])
    setFiles(prev => [...prev, ...selectedFiles].slice(0, 3))
  }
  
  const removeFile = (index: number) => {
    setFiles(prev => prev.filter((_, i) => i !== index))
  }
  
  return (
    <Card>
      <CardContent>
        <h3 className="font-medium text-gray-900 mb-3">Add Progress Update</h3>
        
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
          <Textarea
            placeholder="Describe your progress..."
            error={errors.description?.message}
            {...register('description')}
          />
          
          <div className="flex items-center gap-2">
            <input
              type="file"
              multiple
              accept=".jpg,.jpeg,.png,.pdf"
              onChange={handleFileChange}
              className="hidden"
              id="progress-file"
            />
            <Button
              type="button"
              variant="secondary"
              size="sm"
              onClick={() => document.getElementById('progress-file')?.click()}
            >
              <Upload size={14} className="mr-1" />
              Attach File
            </Button>
            <span className="text-xs text-gray-400">Optional (max 3 files)</span>
          </div>
          
          {files.length > 0 && (
            <div className="space-y-1">
              {files.map((file, index) => (
                <div key={index} className="flex items-center gap-2 text-sm">
                  <span className="truncate flex-1 text-gray-600">{file.name}</span>
                  <button
                    type="button"
                    onClick={() => removeFile(index)}
                    className="text-gray-400 hover:text-danger"
                  >
                    <X size={14} />
                  </button>
                </div>
              ))}
            </div>
          )}
          
          <Button 
            type="submit" 
            className="w-full"
            loading={createProgress.isPending}
            disabled={createProgress.isPending}
          >
            Submit Progress
          </Button>
        </form>
      </CardContent>
    </Card>
  )
}
