import { motion } from 'framer-motion'
import { PlusCircle, UserCheck, ShieldCheck, CheckCircle2 } from 'lucide-react'

const steps = [
  {
    number: '01',
    title: 'Buat Task',
    description: 'Tulis detail pekerjaan harian Anda, tentukan budget (harga pas), dan buat tugas baru dalam hitungan detik.',
    icon: PlusCircle,
    color: 'from-blue-500 to-blue-600',
    bgLight: 'bg-blue-50 text-blue-600 border-blue-100'
  },
  {
    number: '02',
    title: 'Helper Menerima',
    description: 'Helper terdekat yang terverifikasi meninjau tugas Anda dan bersedia mengambil pekerjaan sesuai budget yang Anda tawarkan.',
    icon: UserCheck,
    color: 'from-cyan-500 to-cyan-600',
    bgLight: 'bg-cyan-50 text-cyan-600 border-cyan-100'
  },
  {
    number: '03',
    title: 'Task Dikerjakan',
    description: 'Helper mengunggah bukti progress secara bertahap. Anda dapat memantau pengerjaan secara langsung dan teratur.',
    icon: CheckCircle2,
    color: 'from-purple-500 to-purple-600',
    bgLight: 'bg-purple-50 text-purple-600 border-purple-100'
  },
  {
    number: '04',
    title: 'Pembayaran Aman',
    description: 'Pembayaran disimpan di rekening bersama (escrow) dan hanya akan dilepaskan ke Helper setelah pekerjaan selesai disetujui.',
    icon: ShieldCheck,
    color: 'from-emerald-500 to-emerald-600',
    bgLight: 'bg-emerald-50 text-emerald-600 border-emerald-100'
  }
]

export function HowItWorksSection() {
  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
      <div className="max-w-7xl mx-auto text-center space-y-16">
        {/* Header */}
        <div className="max-w-3xl mx-auto space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Cara Kerja BantuinYuk
          </h2>
          <p className="text-lg text-slate-600">
            Sistem escrow dan pemantauan real-time yang dirancang untuk menjamin keamanan dan kenyamanan kedua belah pihak.
          </p>
        </div>

        {/* Timeline Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
          {/* Connecting Line (Only visible on large screen) */}
          <div className="hidden lg:block absolute top-[28%] left-[12%] right-[12%] h-0.5 bg-slate-100 -z-0" />

          {steps.map((step, idx) => {
            const IconComponent = step.icon
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: '-100px' }}
                transition={{ duration: 0.3, delay: idx * 0.1 }}
                className="relative bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group z-10 flex flex-col items-center text-center space-y-4"
              >
                {/* Step number badge */}
                <span className="absolute top-4 right-4 text-xs font-black text-slate-300 font-mono">
                  {step.number}
                </span>

                {/* Icon Circle */}
                <div className={`p-4 rounded-full border ${step.bgLight} transition-all group-hover:scale-110 shadow-sm`}>
                  <IconComponent size={24} />
                </div>

                <div className="space-y-2">
                  <h3 className="font-bold text-slate-900 text-lg">{step.title}</h3>
                  <p className="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    {step.description}
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
