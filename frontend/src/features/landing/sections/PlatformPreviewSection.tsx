import { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  User, 
  Briefcase, 
  ShieldAlert, 
  LayoutDashboard, 
  PlusCircle, 
  MapPin, 
  CheckCircle2, 
  Users, 
  AlertTriangle,
  Wallet
} from 'lucide-react'


type TabType = 'user' | 'helper' | 'admin'

export function PlatformPreviewSection() {
  const [activeTab, setActiveTab] = useState<TabType>('user')

  return (
    <section className="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 border-y border-slate-100 relative">
      <div className="max-w-7xl mx-auto text-center space-y-12">
        {/* Header */}
        <div className="max-w-3xl mx-auto space-y-4">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Preview Platform BantuinYuk
          </h2>
          <p className="text-lg text-slate-600">
            Intip bagaimana sistem dasbor terintegrasi kami membantu mempermudah manajemen tugas harian Anda, baik sebagai Pengguna, Helper, maupun Admin.
          </p>
        </div>

        {/* Tab Controls */}
        <div className="inline-flex p-1 rounded-xl bg-slate-200/60 backdrop-blur-sm">
          <button
            onClick={() => setActiveTab('user')}
            className={`flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition-all cursor-pointer ${
              activeTab === 'user'
                ? 'bg-white text-blue-600 shadow-md'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <User size={16} />
            <span>Dashboard Pengguna</span>
          </button>
          <button
            onClick={() => setActiveTab('helper')}
            className={`flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition-all cursor-pointer ${
              activeTab === 'helper'
                ? 'bg-white text-blue-600 shadow-md'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <Briefcase size={16} />
            <span>Dashboard Helper</span>
          </button>
          <button
            onClick={() => setActiveTab('admin')}
            className={`flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition-all cursor-pointer ${
              activeTab === 'admin'
                ? 'bg-white text-blue-600 shadow-md'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <ShieldAlert size={16} />
            <span>Dashboard Admin</span>
          </button>
        </div>

        {/* Mockup Container */}
        <div className="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden text-left max-w-5xl mx-auto">
          {/* Mockup Browser Window Header */}
          <div className="bg-slate-100 border-b border-slate-200 px-4 py-3 flex items-center justify-between">
            <div className="flex items-center gap-1.5">
              <span className="w-3 h-3 rounded-full bg-red-400" />
              <span className="w-3 h-3 rounded-full bg-yellow-400" />
              <span className="w-3 h-3 rounded-full bg-green-400" />
              <div className="ml-4 bg-white border border-slate-200/80 px-4 py-0.5 rounded-md text-[11px] text-slate-400 font-mono select-none w-72 truncate">
                https://bantuinYuk.test/{activeTab}/dashboard
              </div>
            </div>
            <span className="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">
              Live Mockup
            </span>
          </div>

          {/* Tab Content Rendering */}
          <div className="p-6 md:p-8 bg-slate-50 min-h-[460px] relative">
            <AnimatePresence mode="wait">
              {activeTab === 'user' && <UserDashboardMockup key="user" />}
              {activeTab === 'helper' && <HelperDashboardMockup key="helper" />}
              {activeTab === 'admin' && <AdminDashboardMockup key="admin" />}
            </AnimatePresence>
          </div>
        </div>
      </div>
    </section>
  )
}

function UserDashboardMockup() {
  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.2 }}
      className="space-y-6"
    >
      {/* Header Info */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 className="text-xl font-bold text-slate-900">Halo, Ahmad Prasetyo 👋</h3>
          <p className="text-xs text-slate-500">Butuh bantuan apa hari ini? Tulis tugas Anda sekarang.</p>
        </div>
        <button className="flex items-center gap-2 bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 shadow-md">
          <PlusCircle size={14} />
          <span>Buat Task Baru</span>
        </button>
      </div>

      {/* Grid Status */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Tugas Aktif</p>
            <p className="text-xl font-bold text-slate-900">1</p>
          </div>
          <span className="text-lg bg-blue-50 p-2 rounded-lg text-blue-600">📝</span>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Tugas Selesai</p>
            <p className="text-xl font-bold text-slate-900">14</p>
          </div>
          <span className="text-lg bg-emerald-50 p-2 rounded-lg text-emerald-600">✅</span>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Saldo Dompet</p>
            <p className="text-xl font-bold text-slate-900">Rp 250.000</p>
          </div>
          <span className="text-lg bg-cyan-50 p-2 rounded-lg text-cyan-600">💰</span>
        </div>
      </div>

      {/* Current Task Card */}
      <div className="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div className="px-4 py-3 bg-slate-100/50 border-b border-slate-200/85 flex items-center justify-between">
          <span className="text-xs font-bold text-slate-700">Tugas Aktif Saat Ini</span>
          <span className="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-bold">Mencari Helper</span>
        </div>
        <div className="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="space-y-2">
            <h4 className="font-bold text-slate-900 text-sm">Pembersihan Halaman Belakang Rumah</h4>
            <div className="flex flex-wrap items-center gap-3 text-xs text-slate-500">
              <span className="flex items-center gap-1"><MapPin size={12} /> Jakarta Selatan</span>
              <span>•</span>
              <span>Kategori: Bersih Rumah</span>
            </div>
          </div>
          <div className="flex items-center gap-4 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
            <div>
              <p className="text-[10px] text-slate-400 text-right">Dana Escrow</p>
              <p className="font-bold text-slate-900 text-sm">Rp 75.000</p>
            </div>
            <button className="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-md">
              Lihat Detail
            </button>
          </div>
        </div>
      </div>
    </motion.div>
  )
}

function HelperDashboardMockup() {
  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.2 }}
      className="space-y-6"
    >
      {/* Header Info */}
      <div>
        <h3 className="text-xl font-bold text-slate-900">Halo, Budi Santoso 👋</h3>
        <p className="text-xs text-slate-500">Silakan kerjakan tugas aktif Anda atau cari pekerjaan baru di sekitar lokasi.</p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Pekerjaan Aktif</p>
            <p className="text-xl font-bold text-slate-900">1</p>
          </div>
          <span className="text-lg bg-amber-50 p-2 rounded-lg text-amber-600">🛠️</span>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Rating</p>
            <p className="text-xl font-bold text-slate-900">4.9 ★</p>
          </div>
          <span className="text-lg bg-emerald-50 p-2 rounded-lg text-emerald-600">⭐</span>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase">Total Pendapatan</p>
            <p className="text-xl font-bold text-slate-900">Rp 1.450.000</p>
          </div>
          <span className="text-lg bg-blue-50 p-2 rounded-lg text-blue-600">💰</span>
        </div>
      </div>

      {/* Active Task progress */}
      <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
        <div className="flex items-center justify-between">
          <span className="text-xs font-bold text-slate-700">Tugas yang Sedang Dikerjakan</span>
          <span className="text-[10px] bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full font-bold">Sedang Dikerjakan</span>
        </div>
        <h4 className="font-bold text-slate-900 text-sm">Pindahan Kost Bulanan (Mahasiswa)</h4>
        
        {/* Progress Timeline Mini-Preview */}
        <div className="bg-slate-50 p-3 rounded-lg border border-slate-200/80 space-y-2">
          <p className="text-[10px] font-bold text-slate-500">Progress Milestone (3/4 Selesai)</p>
          <div className="flex gap-2 items-center text-xs">
            <CheckCircle2 size={12} className="text-emerald-500 flex-shrink-0" />
            <span className="text-slate-600 truncate">Packing barang ke kardus selesai</span>
          </div>
          <div className="flex gap-2 items-center text-xs">
            <CheckCircle2 size={12} className="text-emerald-500 flex-shrink-0" />
            <span className="text-slate-600 truncate">Loading barang ke mobil box selesai</span>
          </div>
          <div className="flex gap-2 items-center text-xs">
            <span className="h-2 w-2 rounded-full bg-blue-500 animate-pulse flex-shrink-0 ml-0.5" />
            <span className="text-slate-800 font-medium truncate">Pengiriman ke kost baru sedang berjalan</span>
          </div>
        </div>
      </div>
    </motion.div>
  )
}

function AdminDashboardMockup() {
  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.2 }}
      className="space-y-6"
    >
      {/* Header Info */}
      <div>
        <h3 className="text-xl font-bold text-slate-900 flex items-center gap-2">
          <LayoutDashboard size={20} className="text-blue-600" />
          <span>Panel Kontrol Admin</span>
        </h3>
        <p className="text-xs text-slate-500">Overview sistem keuangan escrow, review helper baru, dan penyelesaian sengketa.</p>
      </div>

      {/* Stats counters */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between text-slate-400 mb-1">
            <span className="text-[9px] font-bold uppercase">Pengguna</span>
            <Users size={14} />
          </div>
          <p className="text-lg font-bold text-slate-900">165</p>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between text-slate-400 mb-1">
            <span className="text-[9px] font-bold uppercase">Helper Terverifikasi</span>
            <CheckCircle2 size={14} />
          </div>
          <p className="text-lg font-bold text-slate-900">45</p>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between text-slate-400 mb-1">
            <span className="text-[9px] font-bold uppercase">Total Escrow</span>
            <Wallet size={14} />
          </div>
          <p className="text-lg font-bold text-slate-900 text-blue-600">Rp 4.250K</p>
        </div>
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
          <div className="flex items-center justify-between text-slate-400 mb-1">
            <span className="text-[9px] font-bold uppercase">Sengketa</span>
            <AlertTriangle size={14} />
          </div>
          <p className="text-lg font-bold text-red-500">2 Kasus</p>
        </div>
      </div>

      {/* Multi layout view */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {/* Verification queue card */}
        <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
          <p className="text-xs font-bold text-slate-700">Antrean Verifikasi Helper</p>
          <div className="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
            <div className="text-left">
              <p className="text-xs font-bold text-slate-900">Budi Santoso</p>
              <p className="text-[10px] text-slate-500">KTP & SKCK Terlampir</p>
            </div>
            <div className="flex gap-1">
              <button className="text-[9px] font-bold bg-emerald-500 text-white px-2.5 py-1 rounded">Setujui</button>
              <button className="text-[9px] font-bold bg-red-100 text-red-600 px-2 py-1 rounded">Tolak</button>
            </div>
          </div>
        </div>

        {/* Disputes queue card */}
        <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
          <p className="text-xs font-bold text-slate-700">Sengketa Baru</p>
          <div className="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
            <div>
              <p className="text-xs font-bold text-slate-900">Tugas #1024 (Pindahan)</p>
              <p className="text-[10px] text-red-500 font-medium">Bantahan pembayaran oleh Pengguna</p>
            </div>
            <button className="text-[9px] font-bold bg-blue-100 text-blue-600 px-2.5 py-1.5 rounded">
              Arbitrase
            </button>
          </div>
        </div>
      </div>
    </motion.div>
  )
}
