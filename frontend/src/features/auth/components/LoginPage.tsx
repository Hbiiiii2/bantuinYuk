import { useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { useAuthStore } from '@/stores/auth.store'
import { loginSchema, type LoginFormData } from '../validation/auth.schema'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'

export function LoginPage() {
  const navigate = useNavigate()
  const { login, loading, isAuthenticated, getDashboardPath } = useAuthStore()
  
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      email: '',
      password: '',
      remember_me: false
    }
  })
  
  useEffect(() => {
    if (isAuthenticated) {
      navigate(getDashboardPath(), { replace: true })
    }
  }, [isAuthenticated, navigate, getDashboardPath])
  
  const onSubmit = async (data: LoginFormData) => {
    try {
      await login(data.email, data.password)
      navigate(getDashboardPath(), { replace: true })
    } catch (error: any) {
      // Error is handled by the store
    }
  }
  
  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <div className="text-center mb-6">
        <h1 className="text-2xl font-bold text-primary">BantuinYuk</h1>
        <p className="text-gray-500 mt-1">Marketplace outsourcing task</p>
      </div>
      
      <h2 className="text-xl font-semibold text-gray-900 mb-6">Login</h2>
      
      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Input
          label="Email"
          type="email"
          placeholder="john@example.com"
          error={errors.email?.message}
          {...register('email')}
        />
        
        <Input
          label="Password"
          type="password"
          placeholder="••••••••"
          error={errors.password?.message}
          {...register('password')}
        />
        
        <div className="flex items-center justify-between">
          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              className="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
              {...register('remember_me')}
            />
            <span className="text-sm text-gray-600">Remember me</span>
          </label>
          
          <button
            type="button"
            className="text-sm text-primary hover:underline"
            onClick={() => {/* TODO: Implement forgot password */}}
          >
            Forgot password?
          </button>
        </div>
        
        <Button 
          type="submit" 
          className="w-full" 
          loading={loading}
          disabled={loading}
        >
          Login
        </Button>
      </form>
      
      <p className="mt-6 text-center text-sm text-gray-500">
        Don't have an account?{' '}
        <Link to="/register" className="text-primary font-medium hover:underline">
          Register here
        </Link>
      </p>
    </div>
  )
}
