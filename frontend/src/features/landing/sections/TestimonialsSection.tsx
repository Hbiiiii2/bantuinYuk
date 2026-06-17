import { motion } from 'framer-motion'
import { Star } from 'lucide-react'

const testimonials = [
  {
    name: 'Rian Hidayat',
    role: 'Pengguna Jasa',
    text: 'Sangat terbantu untuk pindahan kost kemarin. Prosesnya cepat, helper ramah, dan yang paling penting pembayarannya aman lewat escrow BantuinYuk.',
    avatar: 'RH',
    color: 'bg-blue-600'
  },
  {
    name: 'Siti Rahmawati',
    role: 'Helper Terverifikasi',
    text: 'BantuinYuk membuka jalan buat saya cari penghasilan tambahan di sela-sela waktu luang kuliah. Tarik saldo wallet ke rekening pribadi juga cepat sekali!',
    avatar: 'SR',
    color: 'bg-cyan-600'
  },
  {
    name: 'Aris Setiawan',
    role: 'Pengguna Jasa',
    text: 'Dua kali order bersih-bersih rumah di sini selalu dapat helper yang cekatan. Progress kerjanya difoto satu-satu jadi terpantau dengan sangat rapi.',
    avatar: 'AS',
    color: 'bg-emerald-600'
  }
]

export function TestimonialsSection() {
  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 border-b border-slate-100 relative">
      <div className="max-w-7xl mx-auto space-y-16">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Apa Kata Mereka?
          </h2>
          <p className="text-lg text-slate-600">
            Dengarkan langsung cerita dari para pengguna dan helper yang telah bergabung di platform kami.
          </p>
        </div>

        {/* 3 Columns Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {testimonials.map((testi, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, scale: 0.95 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true, margin: '-50px' }}
              transition={{ duration: 0.3, delay: idx * 0.1 }}
              className="bg-white border border-slate-200/85 p-6 rounded-2xl shadow-sm flex flex-col justify-between text-left space-y-6"
            >
              {/* Rating stars */}
              <div className="flex gap-1 text-amber-400">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} size={16} fill="currentColor" />
                ))}
              </div>

              {/* Text */}
              <p className="text-slate-600 text-xs sm:text-sm leading-relaxed italic">
                "{testi.text}"
              </p>

              {/* Profile Details */}
              <div className="flex items-center gap-3 border-t border-slate-100 pt-4">
                <div className={`h-10 w-10 rounded-full ${testi.color} flex items-center justify-center text-white font-bold text-sm select-none`}>
                  {testi.avatar}
                </div>
                <div>
                  <p className="font-bold text-slate-900 text-sm">{testi.name}</p>
                  <p className="text-slate-400 text-[10px] sm:text-xs">{testi.role}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}
