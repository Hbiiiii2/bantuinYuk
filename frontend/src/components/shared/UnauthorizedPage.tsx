import { useNavigate } from 'react-router-dom'
import { Button } from '@/components/ui/Button'
import { Lock } from 'lucide-react'

export function UnauthorizedPage() {
  const navigate = useNavigate()
  
  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
      <div className="text-gray-400 mb-4">
        <Lock size={64} />
      </div>
      <h1 className="text-4xl font-bold text-gray-900 mb-2">403</h1>
      <p className="text-lg text-gray-600 mb-2">Unauthorized</p>
      <p className="text-sm text-gray-500 text-center max-w-md mb-6">
        You don't have permission to access this page.
      </p>
      <Button onClick={() => navigate('/login')} variant="primary">
        Login
      </Button>
    </div>
  )
}
