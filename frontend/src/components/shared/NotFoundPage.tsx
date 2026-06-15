import { useNavigate } from 'react-router-dom'
import { Button } from '@/components/ui/Button'
import { Search } from 'lucide-react'

export function NotFoundPage() {
  const navigate = useNavigate()
  
  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
      <div className="text-gray-400 mb-4">
        <Search size={64} />
      </div>
      <h1 className="text-4xl font-bold text-gray-900 mb-2">404</h1>
      <p className="text-lg text-gray-600 mb-2">Page not found</p>
      <p className="text-sm text-gray-500 text-center max-w-md mb-6">
        The page you're looking for doesn't exist or has been moved.
      </p>
      <Button onClick={() => navigate('/')} variant="primary">
        Go Home
      </Button>
    </div>
  )
}
