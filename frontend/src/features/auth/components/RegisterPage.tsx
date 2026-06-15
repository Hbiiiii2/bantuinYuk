import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { useAuthStore } from '@/stores/auth.store'
import { registerSchema, type RegisterFormData } from '../validation/auth.schema'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'

export function RegisterPage() {
  const navigate = useNavigate()
  const { register: registerUser, loading, isAuthenticated, getDashboardPath } = useAuthStore()
  const [success, setSuccess] = useState(false)
  
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<RegisterFormData>({
    resolver: zodResolver(registerSchema),
    defaultValues: {
      name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: ''
    }
  })
  
  useEffect(() => {
    if (isAuthenticated) {
      navigate(getDashboardPath(), { replace: true })
    }
  }, [isAuthenticated, navigate, getDashboardPath])
  
  const onSubmit = async (data: RegisterFormData) => {
    try {
      await registerUser(data.name, data.email, data.phone, data.password)
      setSuccess(true)
      setTimeout(() => {
        navigate('/login', { replace: true })
      }, 2000)
    } catch (error: any) {
      // Error is handled by the store
    }
  }
  
  if (success) {
    return (
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
        <div className="w-16 h-16 mx-auto mb-4 bg-success-light rounded-full flex items-center justify-center">
          <svg className="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 className="text-xl font-semibold text-gray-900 mb-2">Registration Successful!</h2>
        <p className="text-gray-500 mb-4">Redirecting to login page...</p>
      </div>
    )
  }
  
  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <div className="text-center mb-6">
        <h1 className="text-2xl font-bold text-primary">BantuinYuk</h1>
        <p className="text-gray-500 mt-1">Marketplace outsourcing task</p>
      </div>
      
      <h2 className="text-xl font-semibold text-gray-900 mb-6">Register</h2>
      
      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Input
          label="Full Name"
          placeholder="John Doe"
          error={errors.name?.message}
          {...register('name')}
        />
        
        <Input
          label="Email"
          type="email"
          placeholder="john@example.com"
          error={errors.email?.message}
          {...register('email')}
        />
        
        <Input
          label="Phone Number"
          type="tel"
          placeholder="081234567890"
          error={errors.phone?.message}
          {...register('phone')}
        />
        
        <Input
          label="Password"
          type="password"
          placeholder="••••••••"
          error={errors.password?.message}
          {...register('password')}
        />
        
        <Input
          label="Confirm Password"
          type="password"
          placeholder="••••••••"
          error={errors.password_confirmation?.message}
          {...register('password_confirmation')}
        />
        
        <Button 
          type="submit" 
          className="w-full" 
          loading={loading}
          disabled={loading}
        >
          Register
        </Button>
      </form>
      
      <p className="mt-6 text-center text-sm text-gray-500">
        Already have an account?{' '}
        <Link to="/login" className="text-primary font-medium hover:underline">
          Login here
        </Link>
      </p>
    </div>
  )
}
