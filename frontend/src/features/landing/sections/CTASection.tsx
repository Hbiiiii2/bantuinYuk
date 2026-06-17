import { useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useAuthStore } from '@/stores/auth.store'
import { ArrowRight } from 'lucide-react'

export function CTASection() {
  const navigate = useNavigate()
  const { isAuthenticated, user } = useAuthStore()

  const handlePrimaryClick = () => {
    if (isAuthenticated && user) {
      navigate(user.role === 'admin' ? '/admin/dashboard' : user.role === 'helper' ? '/helper/dashboard' : '/user/dashboard')
    } else {
      navigate('/login')
    }
  }

  const handleSecondaryClick = () => {
    if (isAuthenticated && user) {
      navigate(user.role === 'admin' ? '/admin/dashboard' : user.role === 'helper' ? '/helper/dashboard' : '/user/dashboard')
    } else {
      navigate('/register?role=helper')
    }
  }

  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
      <div className="max-w-6xl mx-auto">
        <motion.div
          initial={{ opacity: 0, scale: 0.98 }}
          whileInView={{ opacity: 1, scale: 1 }}
          viewport={{ once: true }}
          transition={{ duration: 0.35 }}
          className="bg-gradient-to-tr from-blue-600 via-blue-600 to-cyan-500 rounded-3xl p-8 md:p-12 text-center text-white shadow-xl relative overflow-hidden space-y-6"
        >
          {/* Decorative shapes */}
          <div className="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-3xl -translate-y-24 translate-x-24 pointer-events-none" />
          <div className="absolute bottom-0 left-0 w-80 h-80 bg-cyan-300/20 rounded-full blur-3xl translate-y-24 -translate-x-24 pointer-events-none" />

          <div className="max-w-2xl mx-auto space-y-4 relative z-10">
            <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight">
              Siap Menyelesaikan Tugas Lebih Mudah?
            </h2>
            <p className="text-white/80 text-sm sm:text-base leading-relaxed">
              Bergabunglah dengan ratusan pengguna aktif dan helper terpercaya di wilayah Anda. Buat tugas pertamamu atau mulailah bekerja hari ini!
            </p>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center relative z-10 pt-4">
            <button
              onClick={handlePrimaryClick}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-blue-600 font-extrabold h-12 px-8 rounded-xl shadow-lg transition-all cursor-pointer"
            >
              <span>{isAuthenticated ? 'Ke Dashboard Utama' : 'Mulai Sekarang'}</span>
              <ArrowRight size={18} />
            </button>
            <button
              onClick={handleSecondaryClick}
              className="w-full sm:w-auto inline-flex items-center justify-center bg-white/15 hover:bg-white/25 text-white font-bold h-12 px-8 rounded-xl border border-white/20 transition-all cursor-pointer"
            >
              {isAuthenticated ? 'Lihat Dasbor Helper' : 'Daftar Helper'}
            </button>
          </div>
        </motion.div>
      </div>
    </section>
  )
}
