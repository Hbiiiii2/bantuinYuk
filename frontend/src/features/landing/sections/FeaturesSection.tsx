import { motion } from 'framer-motion'
import { 
  ShieldCheck, 
  Lock, 
  RefreshCw, 
  Bell, 
  Star, 
  Headphones 
} from 'lucide-react'

const features = [
  {
    title: 'Helper Terverifikasi',
    description: 'Setiap helper wajib melewati proses kurasi KTP, nomor telepon, dokumen pendukung, dan verifikasi admin manual.',
    icon: ShieldCheck,
    bg: 'bg-emerald-50 text-emerald-600'
  },
  {
    title: 'Pembayaran Aman',
    description: 'Menggunakan sistem rekening bersama (escrow). Dana dilepaskan hanya jika pekerjaan telah selesai diverifikasi.',
    icon: Lock,
    bg: 'bg-blue-50 text-blue-600'
  },
  {
    title: 'Progress Real-Time',
    description: 'Pantau status pengerjaan secara langsung melalui antrean milestone tugas yang diunggah helper secara berkala.',
    icon: RefreshCw,
    bg: 'bg-cyan-50 text-cyan-600'
  },
  {
    title: 'Notifikasi Otomatis',
    description: 'Dapatkan pemberitahuan seketika saat helper menerima tugas, menyelesaikan progress, atau merilis obrolan baru.',
    icon: Bell,
    bg: 'bg-amber-50 text-amber-600'
  },
  {
    title: 'Rating & Review',
    description: 'Ulasan transparan dari sesama pengguna membantu Anda memilih Helper dengan performa kerja terbaik.',
    icon: Star,
    bg: 'bg-yellow-50 text-yellow-600'
  },
  {
    title: 'Dukungan Admin',
    description: 'Tersedia tim penengah arbitrase yang siap membantu menyelesaikan perselisihan atau pembatalan secara adil.',
    icon: Headphones,
    bg: 'bg-purple-50 text-purple-600'
  }
]

export function FeaturesSection() {
  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
      <div className="max-w-7xl mx-auto space-y-16">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Keunggulan BantuinYuk
          </h2>
          <p className="text-lg text-slate-600">
            Platform modern yang dibangun untuk menunjang produktivitas dengan mengutamakan aspek keamanan transaksi.
          </p>
        </div>

        {/* Grid 6 Features */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, idx) => {
            const IconComponent = feature.icon
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: '-50px' }}
                transition={{ duration: 0.3, delay: idx * 0.05 }}
                className="flex flex-col text-left space-y-3"
              >
                {/* Icon wrapper */}
                <div className={`p-3.5 rounded-2xl w-fit ${feature.bg} shadow-sm border border-slate-100`}>
                  <IconComponent size={24} />
                </div>

                <div className="space-y-1">
                  <h3 className="font-bold text-slate-900 text-lg">
                    {feature.title}
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    {feature.description}
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
