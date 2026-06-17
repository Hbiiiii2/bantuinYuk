import { useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useAuthStore } from '@/stores/auth.store'
import { Search, PenTool, CheckCircle, ArrowRight } from 'lucide-react'

export function HelperSection() {
  const navigate = useNavigate()
  const { isAuthenticated, user } = useAuthStore()

  const handleCtaClick = () => {
    if (isAuthenticated && user) {
      navigate(user.role === 'admin' ? '/admin/dashboard' : user.role === 'helper' ? '/helper/dashboard' : '/user/dashboard')
    } else {
      navigate('/register?role=helper')
    }
  }

  const steps = [
    {
      title: '1. Cari Task',
      description: 'Filter pekerjaan harian berdasarkan kategori dan lokasi terdekat dari tempat Anda berada.',
      icon: Search
    },
    {
      title: '2. Kerjakan Pekerjaan',
      description: 'Lakukan instruksi tugas dengan baik dan unggah bukti progress kerja Anda secara berkala.',
      icon: PenTool
    },
    {
      title: '3. Tarik Penghasilan',
      description: 'Dana dicairkan langsung ke wallet akun Anda setelah disetujui, dan bisa ditarik langsung ke rekening bank.',
      icon: CheckCircle
    }
  ]

  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 text-white overflow-hidden relative">
      {/* Decorative Blur Backgrounds */}
      <div className="absolute inset-0 z-0">
        <div className="absolute top-[-20%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-blue-500/10 blur-[120px] pointer-events-none" />
        <div className="absolute bottom-[-20%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-cyan-500/10 blur-[120px] pointer-events-none" />
      </div>

      <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center relative z-10 w-full text-left">
        {/* Left: Headline & Steps */}
        <motion.div
          initial={{ opacity: 0, x: -30 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true, margin: '-100px' }}
          transition={{ duration: 0.4 }}
          className="space-y-8"
        >
          <div className="space-y-4">
            <span className="text-blue-400 text-xs font-bold uppercase tracking-wider">Mulai Menghasilkan Uang</span>
            <h2 className="text-3xl sm:text-4xl font-extrabold tracking-tight">
              Ubah Waktu Luang Menjadi Penghasilan tambahan
            </h2>
            <p className="text-slate-400 text-sm sm:text-base leading-relaxed max-w-lg">
              Bergabunglah sebagai Helper terverifikasi di BantuinYuk. Pilih tugas harian yang sesuai dengan kemampuan dan waktu luang Anda, selesaikan tugasnya, dan dapatkan bayaran aman.
            </p>
          </div>

          {/* Steps List */}
          <div className="space-y-6 max-w-lg">
            {steps.map((step, idx) => {
              const IconComp = step.icon
              return (
                <div key={idx} className="flex gap-4">
                  <div className="flex-shrink-0 h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-blue-400">
                    <IconComp size={18} />
                  </div>
                  <div className="space-y-1">
                    <h3 className="font-bold text-white text-base">{step.title}</h3>
                    <p className="text-slate-400 text-xs sm:text-sm leading-relaxed">{step.description}</p>
                  </div>
                </div>
              )
            })}
          </div>

          <button
            onClick={handleCtaClick}
            className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold h-12 px-8 rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/25 transition-all cursor-pointer"
          >
            <span>{isAuthenticated ? 'Ke Dashboard Helper' : 'Daftar Sebagai Helper'}</span>
            <ArrowRight size={16} />
          </button>
        </motion.div>

        {/* Right: Graphic mockup representing wallet earnings / task alerts */}
        <motion.div
          initial={{ opacity: 0, x: 30 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true, margin: '-100px' }}
          transition={{ duration: 0.4, delay: 0.1 }}
          className="flex items-center justify-center"
        >
          <div className="w-full max-w-[420px] bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-md shadow-2xl space-y-6 relative">
            <span className="absolute top-4 right-4 bg-emerald-500/80 text-[10px] text-white font-bold px-2 py-0.5 rounded-full">Active Balance</span>
            
            {/* Earnings header */}
            <div className="text-left space-y-1 border-b border-white/5 pb-4">
              <p className="text-xs text-white/50">Total Pendapatan Anda</p>
              <p className="text-3xl font-black text-white">Rp 1.450.000</p>
            </div>

            {/* Simulated transactions list */}
            <div className="space-y-3">
              <p className="text-[10px] font-bold text-slate-500 text-left uppercase">Riwayat Pekerjaan Terakhir</p>
              
              <div className="flex items-center justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-left">
                <div className="space-y-0.5">
                  <p className="text-xs font-bold">Pembersihan Pagar Depan</p>
                  <p className="text-[10px] text-white/40">Selesai • 16 Juni 2026</p>
                </div>
                <span className="text-xs text-emerald-400 font-extrabold">+Rp 120.000</span>
              </div>

              <div className="flex items-center justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-left">
                <div className="space-y-0.5">
                  <p className="text-xs font-bold">Pindahan Kost Harian</p>
                  <p className="text-[10px] text-white/40">Selesai • 14 Juni 2026</p>
                </div>
                <span className="text-xs text-emerald-400 font-extrabold">+Rp 350.000</span>
              </div>

              <div className="flex items-center justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-left">
                <div className="space-y-0.5">
                  <p className="text-xs font-bold">Belanja Bulanan Superindo</p>
                  <p className="text-[10px] text-white/40">Selesai • 12 Juni 2026</p>
                </div>
                <span className="text-xs text-emerald-400 font-extrabold">+Rp 85.000</span>
              </div>
            </div>

            {/* Withdraw button mock */}
            <div className="pt-2">
              <div className="w-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold py-3 rounded-xl text-center select-none">
                ✓ Pencairan Otomatis via Escrow
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  )
}
