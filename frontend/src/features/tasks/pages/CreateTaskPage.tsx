import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { Upload, X } from 'lucide-react'
import { useCreateTask, useCategories } from '../hooks'
import { createTaskSchema, type CreateTaskFormData } from '../schemas/task.schema'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent } from '@/components/ui/Card'
import { Input } from '@/components/ui/Input'
import { Textarea } from '@/components/ui/Textarea'
import { Button } from '@/components/ui/Button'

export function CreateTaskPage() {
  const navigate = useNavigate()
  const createMutation = useCreateTask()
  const { data: categories } = useCategories()
  const [files, setFiles] = useState<File[]>([])
  
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<CreateTaskFormData>({
    resolver: zodResolver(createTaskSchema),
    defaultValues: {
      title: '',
      description: '',
      price: 0,
      category_id: 0,
      deadline_start: '',
      deadline_end: '',
      location: ''
    }
  })
  
  const onSubmit = async (data: CreateTaskFormData) => {
    try {
      const task = await createMutation.mutateAsync(data)
      navigate(`/user/tasks/${task.id}`)
    } catch (error) {
      // Error handled by mutation
    }
  }
  
  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFiles = Array.from(e.target.files || [])
    setFiles(prev => [...prev, ...selectedFiles].slice(0, 5))
  }
  
  const removeFile = (index: number) => {
    setFiles(prev => prev.filter((_, i) => i !== index))
  }
  
  return (
    <div>
      <PageHeader 
        title="Create Task" 
        showBack
      />
      
      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Card>
          <CardContent className="space-y-4">
            <Input
              label="Title"
              placeholder="What do you need help with?"
              error={errors.title?.message}
              {...register('title')}
            />
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Category
              </label>
              <select
                className="w-full h-11 px-4 rounded-lg border border-gray-200 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                {...register('category_id', { valueAsNumber: true })}
              >
                <option value={0}>Select a category</option>
                {categories?.map((cat) => (
                  <option key={cat.id} value={cat.id}>{cat.name}</option>
                ))}
              </select>
              {errors.category_id && (
                <p className="text-sm text-danger mt-1">{errors.category_id.message}</p>
              )}
            </div>
            
            <Input
              label="Price (IDR)"
              type="number"
              placeholder="500000"
              error={errors.price?.message}
              {...register('price', { valueAsNumber: true })}
            />
            
            <Textarea
              label="Description"
              placeholder="Describe what you need in detail..."
              error={errors.description?.message}
              {...register('description')}
            />
            
            <div className="grid grid-cols-2 gap-3">
              <Input
                label="Start Date"
                type="datetime-local"
                error={errors.deadline_start?.message}
                {...register('deadline_start')}
              />
              
              <Input
                label="End Date"
                type="datetime-local"
                error={errors.deadline_end?.message}
                {...register('deadline_end')}
              />
            </div>
            
            <Input
              label="Location (Optional)"
              placeholder="Jl. Sudirman No. 123"
              {...register('location')}
            />
          </CardContent>
        </Card>
        
        {/* Attachments */}
        <Card>
          <CardContent>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Attachments (Optional)
            </label>
            
            <div className="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center">
              <Upload size={32} className="mx-auto mb-2 text-gray-400" />
              <p className="text-sm text-gray-600 mb-2">
                Drag & drop files here, or click to select
              </p>
              <p className="text-xs text-gray-400 mb-3">
                JPG, PNG, PDF up to 10MB each (max 5 files)
              </p>
              <input
                type="file"
                multiple
                accept=".jpg,.jpeg,.png,.pdf"
                onChange={handleFileChange}
                className="hidden"
                id="file-upload"
              />
              <Button
                type="button"
                variant="secondary"
                size="sm"
                onClick={() => document.getElementById('file-upload')?.click()}
              >
                Select Files
              </Button>
            </div>
            
            {files.length > 0 && (
              <div className="mt-3 space-y-2">
                {files.map((file, index) => (
                  <div
                    key={index}
                    className="flex items-center gap-2 p-2 bg-gray-50 rounded-lg"
                  >
                    <span className="text-sm text-gray-600 truncate flex-1">
                      {file.name}
                    </span>
                    <span className="text-xs text-gray-400">
                      {(file.size / 1024 / 1024).toFixed(2)} MB
                    </span>
                    <button
                      type="button"
                      onClick={() => removeFile(index)}
                      className="text-gray-400 hover:text-danger"
                    >
                      <X size={16} />
                    </button>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>
        
        <Button
          type="submit"
          className="w-full"
          loading={createMutation.isPending}
          disabled={createMutation.isPending}
        >
          Create Task
        </Button>
      </form>
    </div>
  )
}
