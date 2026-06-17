import { motion } from 'framer-motion'
import { Sparkles, Home, ShoppingCart, Wrench, Truck, MoreHorizontal } from 'lucide-react'

const services = [
  {
    title: 'Bersih Rumah',
    description: 'Sapu, pel, pembersihan toilet, cuci piring, hingga merapikan kebun harian.',
    icon: Sparkles,
    color: 'bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white'
  },
  {
    title: 'Pindahan',
    description: 'Jasa angkut barang kos, apartemen, atau rumah ke lokasi baru Anda dengan aman.',
    icon: Truck,
    color: 'bg-cyan-50 text-cyan-600 border-cyan-100 hover:bg-cyan-600 hover:text-white'
  },
  {
    title: 'Belanja Titipan',
    description: 'Bantuan antre dan belanja kebutuhan bulanan, pasar harian, atau makanan titipan.',
    icon: ShoppingCart,
    color: 'bg-emerald-50 text-emerald-600 border-emerald-100 hover:bg-emerald-600 hover:text-white'
  },
  {
    title: 'Perbaikan Rumah',
    description: 'Mengatasi kran bocor, perbaikan lampu, instalasi kipas, hingga cat dinding.',
    icon: Wrench,
    color: 'bg-amber-50 text-amber-600 border-amber-100 hover:bg-amber-600 hover:text-white'
  },
  {
    title: 'Antar Barang',
    description: 'Kirim dokumen penting, paket dagangan, atau titipan mendadak ke berbagai tempat.',
    icon: Home,
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100 hover:bg-indigo-600 hover:text-white'
  },
  {
    title: 'Lainnya',
    description: 'Punya kebutuhan tugas khusus lainnya? Jelaskan secara detail dalam deskripsi tugas.',
    icon: MoreHorizontal,
    color: 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-800 hover:text-white'
  }
]

export function PopularServicesSection() {
  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 border-b border-slate-100 relative">
      <div className="max-w-7xl mx-auto space-y-16">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Layanan Terpopuler
          </h2>
          <p className="text-lg text-slate-600">
            Pekerjaan harian apa pun yang Anda butuhkan, Helper kami siap datang membantu kapan saja.
          </p>
        </div>

        {/* Grid 6 Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((svc, idx) => {
            const IconComponent = svc.icon
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, scale: 0.95 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true, margin: '-50px' }}
                transition={{ duration: 0.25, delay: idx * 0.05 }}
                whileHover={{ y: -6 }}
                className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all group flex flex-col items-start text-left space-y-4"
              >
                {/* Icon wrapper */}
                <div className={`p-3 rounded-xl border transition-all duration-300 ${svc.color}`}>
                  <IconComponent size={22} />
                </div>

                <div className="space-y-2">
                  <h3 className="font-bold text-slate-900 text-lg group-hover:text-blue-600 transition-colors">
                    {svc.title}
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    {svc.description}
                  </p>
                </div>
              </motion.div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
