import { useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useAuthStore } from '@/stores/auth.store'
import { ShieldCheck, Lock, Headphones, ArrowRight } from 'lucide-react'

export function HeroSection() {
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
    <section className="relative min-h-[calc(100vh-3.5rem)] flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/30 to-cyan-50/20 pt-16 pb-12 px-4 sm:px-6 lg:px-8">
      {/* Background patterns */}
      <div className="absolute inset-0 z-0 overflow-hidden">
        <div className="absolute top-[20%] left-[-10%] w-[45vw] h-[45vw] rounded-full bg-blue-300/10 blur-[120px] pointer-events-none" />
        <div className="absolute bottom-[10%] right-[-10%] w-[40vw] h-[40vw] rounded-full bg-cyan-300/10 blur-[100px] pointer-events-none" />
      </div>

      <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center relative z-10 w-full">
        {/* Left: Marketing Content */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.4 }}
          className="flex flex-col text-left space-y-6"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold w-fit">
            <span className="flex h-2 w-2 rounded-full bg-blue-500 animate-pulse" />
            Platform Jasa Harian No. 1 di Indonesia
          </div>

          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
            Bingung Cari Bantuan Harian? <br />
            <span className="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
              Bantuin Yuk Aja!
            </span>
          </h1>

          <p className="text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
            Temukan helper terpercaya untuk membantu pekerjaan rumah, pindahan, belanja titipan, antar barang, hingga kebutuhan mendadak dengan mudah dan aman.
          </p>

          {/* Action Buttons */}
          <div className="flex flex-col sm:flex-row gap-3 pt-2">
            <button
              onClick={handlePrimaryClick}
              className="inline-flex items-center justify-center gap-2 h-12 px-8 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all cursor-pointer"
            >
              <span>{isAuthenticated ? 'Ke Dashboard' : 'Cari Bantuan'}</span>
              <ArrowRight size={18} />
            </button>
            <button
              onClick={handleSecondaryClick}
              className="inline-flex items-center justify-center h-12 px-8 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-semibold border border-slate-200 shadow-sm hover:border-slate-300 transition-all cursor-pointer"
            >
              {isAuthenticated ? 'Lihat Tugas Anda' : 'Jadi Helper'}
            </button>
          </div>

          {/* Trust Badges */}
          <div className="border-t border-slate-200/80 pt-6 flex flex-wrap gap-x-6 gap-y-3">
            <div className="flex items-center gap-2 text-slate-700">
              <div className="p-1 rounded-lg bg-emerald-50 text-emerald-600">
                <ShieldCheck size={18} />
              </div>
              <span className="text-sm font-medium">Helper Terverifikasi</span>
            </div>
            <div className="flex items-center gap-2 text-slate-700">
              <div className="p-1 rounded-lg bg-blue-50 text-blue-600">
                <Lock size={18} />
              </div>
              <span className="text-sm font-medium">Pembayaran Aman</span>
            </div>
            <div className="flex items-center gap-2 text-slate-700">
              <div className="p-1 rounded-lg bg-cyan-50 text-cyan-600">
                <Headphones size={18} />
              </div>
              <span className="text-sm font-medium">Dukungan Admin</span>
            </div>
          </div>
        </motion.div>

        {/* Right: Graphic Mockup */}
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.4, delay: 0.1 }}
          className="relative flex items-center justify-center lg:justify-end"
        >
          <div className="relative w-full max-w-[480px] aspect-square rounded-3xl bg-gradient-to-tr from-blue-600 to-cyan-400 p-8 shadow-2xl overflow-hidden flex flex-col justify-between text-white">
            {/* Abstract Decorative Graphics */}
            <div className="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-2xl -translate-y-12 translate-x-12" />
            <div className="absolute bottom-0 left-0 w-48 h-48 bg-cyan-300/20 rounded-full blur-xl translate-y-12 -translate-x-12" />

            {/* Header portion of graphic */}
            <div className="relative z-10 flex items-center justify-between border-b border-white/10 pb-4">
              <div className="flex items-center gap-2">
                <span className="h-3 w-3 rounded-full bg-red-400" />
                <span className="h-3 w-3 rounded-full bg-yellow-400" />
                <span className="h-3 w-3 rounded-full bg-green-400" />
              </div>
              <span className="text-xs bg-white/20 px-2 py-0.5 rounded-full backdrop-blur-md">Secure Platform</span>
            </div>

            {/* Content portion of graphic: Interactive Floating Cards */}
            <div className="relative z-10 flex-1 flex flex-col justify-center space-y-4 py-6">
              {/* Task Request Card */}
              <motion.div 
                whileHover={{ y: -4 }}
                className="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl flex items-center justify-between shadow-lg"
              >
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">📦</div>
                  <div className="text-left">
                    <p className="text-xs text-white/70">Pekerjaan Aktif</p>
                    <p className="text-sm font-bold">Pindahan Kos Mahasiswa</p>
                  </div>
                </div>
                <span className="text-xs bg-amber-500/80 px-2 py-1 rounded-lg font-bold">Dalam Proses</span>
              </motion.div>

              {/* Helper Card */}
              <motion.div 
                whileHover={{ y: -4 }}
                className="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl flex items-center justify-between shadow-lg self-end w-[90%]"
              >
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-xl bg-white/20 overflow-hidden flex items-center justify-center text-lg font-bold">🧑‍🔧</div>
                  <div className="text-left">
                    <p className="text-xs text-white/70">Helper Ditugaskan</p>
                    <p className="text-sm font-bold">Budi Santoso (Terverifikasi)</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-xs text-emerald-300 font-bold">Rating 4.9</p>
                  <p className="text-[10px] text-white/60">120+ Selesai</p>
                </div>
              </motion.div>

              {/* Payment Escrow Card */}
              <motion.div 
                whileHover={{ y: -4 }}
                className="bg-white/15 backdrop-blur-md border border-white/25 p-4 rounded-2xl flex items-center justify-between shadow-lg w-[85%]"
              >
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">🛡️</div>
                  <div className="text-left">
                    <p className="text-xs text-white/70">Escrow System</p>
                    <p className="text-sm font-bold">Pembayaran Diamankan</p>
                  </div>
                </div>
                <span className="text-xs bg-emerald-500/80 px-2.5 py-1 rounded-lg font-bold">Rp 150.000</span>
              </motion.div>
            </div>

            {/* Footer portion of graphic */}
            <div className="relative z-10 border-t border-white/10 pt-4 flex items-center justify-between text-xs text-white/60">
              <span>BantuinYuk Mobile App</span>
              <span>v1.0.0</span>
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  )
}
