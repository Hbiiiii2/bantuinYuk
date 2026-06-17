import { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { ChevronDown } from 'lucide-react'

const faqs = [
  {
    question: 'Apa itu BantuinYuk?',
    answer: 'BantuinYuk adalah platform marketplace jasa harian berbasis web yang menghubungkan secara langsung pencari bantuan (User) dengan penyedia bantuan harian (Helper) yang berada di sekitar lokasi terdekat untuk berbagai kebutuhan seperti bersih rumah, pindahan, belanja, dan perbaikan.'
  },
  {
    question: 'Bagaimana sistem pembayaran escrow (rekening bersama) bekerja?',
    answer: 'Setelah User membuat task, dana pengerjaan wajib didepositkan terlebih dahulu ke sistem rekening bersama (escrow) BantuinYuk. Dana ini ditahan secara aman oleh platform selama pengerjaan, dan hanya akan ditransfer ke wallet Helper setelah pekerjaan disetujui selesai oleh User.'
  },
  {
    question: 'Apakah helper di BantuinYuk terpercaya?',
    answer: 'Ya. Setiap Helper yang terdaftar harus melewati proses kurasi dokumen identitas berupa KTP, nomor handphone aktif, serta foto bukti identitas pendukung lainnya. Status akun mereka ditinjau dan diverifikasi secara manual oleh Admin sebelum diizinkan menerima pekerjaan.'
  },
  {
    question: 'Bagaimana jika hasil kerja helper tidak memuaskan atau terjadi sengketa?',
    answer: 'Jika terjadi perselisihan (misalnya helper tidak menyelesaikan pekerjaan dengan baik atau menuntut pembayaran yang tidak sesuai), User atau Helper dapat mengajukan komplain sehingga status tugas menjadi "Dispute". Tim Admin akan bertindak sebagai arbiter netral untuk meninjau bukti progress kerja dan memutuskan pembagian dana escrow secara adil.'
  },
  {
    question: 'Apakah ada biaya pendaftaran saat menggunakan platform?',
    answer: 'Pendaftaran akun baik sebagai User maupun Helper 100% gratis. BantuinYuk tidak memungut biaya bulanan. Kami hanya mengenakan biaya administrasi transaksi yang sangat kecil yang dipotong otomatis saat pelepasan saldo escrow.'
  },
  {
    question: 'Bagaimana cara mendaftar menjadi helper untuk bekerja?',
    answer: 'Anda cukup mendaftar akun di BantuinYuk melalui halaman register, pilih peran sebagai "Helper", lengkapi profil Anda, serta unggah dokumen identitas Anda pada menu verifikasi profile. Setelah disetujui oleh admin, Anda bisa langsung mulai mengambil tugas aktif di sekitar Anda.'
  }
]

export function FAQSection() {
  const [openIndex, setOpenIndex] = useState<number | null>(null)

  const toggleFAQ = (index: number) => {
    setOpenIndex(openIndex === index ? null : index)
  }

  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
      <div className="max-w-4xl mx-auto space-y-16">
        {/* Header */}
        <div className="text-center space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Pertanyaan yang Sering Diajukan
          </h2>
          <p className="text-lg text-slate-600">
            Temukan jawaban cepat mengenai keamanan, sistem pembayaran, verifikasi helper, dan operasional platform.
          </p>
        </div>

        {/* Accordion List */}
        <div className="space-y-4">
          {faqs.map((faq, idx) => {
            const isOpen = openIndex === idx
            return (
              <div 
                key={idx}
                className="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm transition-colors hover:border-slate-300"
              >
                <button
                  onClick={() => toggleFAQ(idx)}
                  className="w-full flex items-center justify-between p-5 text-left font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50/50 transition-colors cursor-pointer"
                >
                  <span>{faq.question}</span>
                  <motion.div
                    animate={{ rotate: isOpen ? 180 : 0 }}
                    transition={{ duration: 0.2 }}
                    className="text-slate-400 flex-shrink-0 ml-4"
                  >
                    <ChevronDown size={20} />
                  </motion.div>
                </button>

                <AnimatePresence initial={false}>
                  {isOpen && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: 'auto', opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.2 }}
                      className="overflow-hidden"
                    >
                      <div className="p-5 pt-0 border-t border-slate-100 text-xs sm:text-sm text-slate-500 leading-relaxed bg-slate-50/20 text-left">
                        {faq.answer}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
