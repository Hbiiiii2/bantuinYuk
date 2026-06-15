import { z } from 'zod'

export const createTaskSchema = z.object({
  title: z
    .string()
    .min(1, 'Title is required')
    .min(5, 'Title must be at least 5 characters')
    .max(255, 'Title must be less than 255 characters'),
  description: z
    .string()
    .min(1, 'Description is required')
    .min(10, 'Description must be at least 10 characters'),
  price: z
    .number()
    .min(1, 'Price is required')
    .min(1000, 'Minimum price is Rp 1,000'),
  category_id: z
    .number()
    .min(1, 'Category is required'),
  deadline_start: z
    .string()
    .min(1, 'Start date is required'),
  deadline_end: z
    .string()
    .min(1, 'End date is required'),
  location: z
    .string()
    .optional()
}).refine(
  (data) => {
    const start = new Date(data.deadline_start)
    const end = new Date(data.deadline_end)
    return end >= start
  },
  {
    message: 'End date must be after start date',
    path: ['deadline_end']
  }
)

export type CreateTaskFormData = z.infer<typeof createTaskSchema>

export const taskFilterSchema = z.object({
  search: z.string().optional(),
  status: z.string().optional(),
  category_id: z.number().optional(),
  page: z.number().optional(),
  per_page: z.number().optional()
})

export type TaskFilterData = z.infer<typeof taskFilterSchema>
