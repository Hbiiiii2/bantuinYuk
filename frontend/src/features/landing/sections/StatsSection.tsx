import { useEffect, useState, useRef } from 'react'
import { motion } from 'framer-motion'

interface CounterProps {
  target: number
  suffix?: string
  duration?: number
}

function Counter({ target, suffix = '', duration = 800 }: CounterProps) {
  const [count, setCount] = useState(0)
  const [hasStarted, setHasStarted] = useState(false)
  const containerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setHasStarted(true)
        }
      },
      { threshold: 0.1 }
    )

    if (containerRef.current) {
      observer.observe(containerRef.current)
    }

    return () => {
      observer.disconnect()
    }
  }, [])

  useEffect(() => {
    if (!hasStarted) return

    let startTimestamp: number | null = null
    const step = (timestamp: number) => {
      if (!startTimestamp) startTimestamp = timestamp
      const progress = timestamp - startTimestamp
      const percentage = Math.min(progress / duration, 1)
      setCount(Math.floor(percentage * target))

      if (percentage < 1) {
        requestAnimationFrame(step)
      } else {
        setCount(target)
      }
    }

    requestAnimationFrame(step)
  }, [hasStarted, target, duration])

  return (
    <div ref={containerRef} className="text-4xl sm:text-5xl font-black text-blue-600 tracking-tight font-sans">
      {count}{suffix}
    </div>
  )
}

export function StatsSection() {
  const statsList = [
    {
      target: 120,
      suffix: '+',
      label: 'Pengguna Aktif',
      desc: 'Mencari bantuan harian'
    },
    {
      target: 45,
      suffix: '+',
      label: 'Helper Terverifikasi',
      desc: 'Telah melalui kurasi manual'
    },
    {
      target: 350,
      suffix: '+',
      label: 'Tugas Selesai',
      desc: 'Berbagai jenis kebutuhan'
    },
    {
      target: 99,
      suffix: '%',
      label: 'Tingkat Keberhasilan',
      desc: 'Kepuasan hasil kerja helper'
    }
  ]

  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-slate-100">
      <div className="max-w-7xl mx-auto">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12">
          {statsList.map((stat, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.3, delay: idx * 0.05 }}
              className="text-center space-y-2 flex flex-col items-center justify-center p-4 border border-slate-100 rounded-2xl bg-slate-50/40 hover:bg-slate-50 transition-colors"
            >
              <Counter target={stat.target} suffix={stat.suffix} />
              <div className="space-y-1">
                <p className="font-bold text-slate-800 text-sm sm:text-base">{stat.label}</p>
                <p className="text-slate-400 text-[10px] sm:text-xs leading-relaxed">{stat.desc}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}
