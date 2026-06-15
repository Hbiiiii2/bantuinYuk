import { useEffect, useState } from 'react'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AppRouter } from '@/app/routes/AppRouter'
import { useAuthStore } from '@/stores/auth.store'
import { LoadingSpinner } from '@/components/shared/LoadingSpinner'
import './styles/globals.css'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 5 * 60 * 1000,
      retry: 1,
      refetchOnWindowFocus: false
    },
    mutations: {
      retry: 0
    }
  }
})

function AppContent() {
  const { hydrate, initialized } = useAuthStore()
  const [hydrating, setHydrating] = useState(true)
  
  useEffect(() => {
    const init = async () => {
      await hydrate()
      setHydrating(false)
    }
    init()
  }, [hydrate])
  
  if (hydrating || !initialized) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <LoadingSpinner size="lg" />
          <p className="mt-4 text-gray-500">Loading...</p>
        </div>
      </div>
    )
  }
  
  return <AppRouter />
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AppContent />
    </QueryClientProvider>
  )
}

export default App
