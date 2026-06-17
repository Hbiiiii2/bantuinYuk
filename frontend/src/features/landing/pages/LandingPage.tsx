import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuthStore } from '@/stores/auth.store'
import { Sparkles, Menu, X, ArrowRight } from 'lucide-react'

// Import Sections
import { HeroSection } from '../sections/HeroSection'
import { PlatformPreviewSection } from '../sections/PlatformPreviewSection'
import { HowItWorksSection } from '../sections/HowItWorksSection'
import { PopularServicesSection } from '../sections/PopularServicesSection'
import { FeaturesSection } from '../sections/FeaturesSection'
import { HelperSection } from '../sections/HelperSection'
import { StatsSection } from '../sections/StatsSection'
import { TestimonialsSection } from '../sections/TestimonialsSection'
import { FAQSection } from '../sections/FAQSection'
import { CTASection } from '../sections/CTASection'
import { FooterSection } from '../sections/FooterSection'

export function LandingPage() {
  const navigate = useNavigate()
  const { isAuthenticated, user } = useAuthStore()
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)

  // SEO setup on mount
  useEffect(() => {
    document.title = 'Bantuin Yuk - Marketplace Jasa Harian Terpercaya'
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', 'Temukan helper terpercaya untuk membantu berbagai kebutuhan harian dengan mudah, aman, dan cepat melalui Bantuin Yuk.')
    }
  }, [])

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  const handleNavClick = (id: string) => {
    setMobileMenuOpen(false)
    const el = document.getElementById(id)
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' })
    }
  }

  const handleCtaClick = () => {
    if (isAuthenticated && user) {
      navigate(user.role === 'admin' ? '/admin/dashboard' : user.role === 'helper' ? '/helper/dashboard' : '/user/dashboard')
    } else {
      navigate('/login')
    }
  }

  return (
    <div className="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans antialiased">
      {/* Sticky Header */}
      <header 
        className={`sticky top-0 z-50 transition-all duration-300 w-full ${
          scrolled 
            ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-200/60 py-3' 
            : 'bg-transparent py-5'
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
          {/* Logo */}
          <div className="flex items-center gap-2 cursor-pointer select-none" onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}>
            <div className="p-1.5 rounded-lg bg-blue-600 text-white">
              <Sparkles size={18} />
            </div>
            <span className="text-xl font-bold tracking-tight text-slate-900">BantuinYuk</span>
          </div>

          {/* Desktop Nav */}
          <nav className="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
            <button onClick={() => handleNavClick('how-it-works')} className="hover:text-blue-600 transition-colors cursor-pointer">Cara Kerja</button>
            <button onClick={() => handleNavClick('services')} className="hover:text-blue-600 transition-colors cursor-pointer">Layanan</button>
            <button onClick={() => handleNavClick('helper')} className="hover:text-blue-600 transition-colors cursor-pointer">Gabung Helper</button>
            <button onClick={() => handleNavClick('faq')} className="hover:text-blue-600 transition-colors cursor-pointer">FAQ</button>
          </nav>

          {/* Action button */}
          <div className="hidden md:block">
            <button
              onClick={handleCtaClick}
              className="inline-flex items-center justify-center gap-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white h-9 px-5 rounded-lg shadow-sm transition-all cursor-pointer"
            >
              <span>{isAuthenticated ? 'Ke Dashboard' : 'Mulai Sekarang'}</span>
              <ArrowRight size={14} />
            </button>
          </div>

          {/* Mobile menu trigger */}
          <button 
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 hover:bg-slate-100 rounded-lg text-slate-600 cursor-pointer"
            aria-label="Toggle menu"
          >
            {mobileMenuOpen ? <X size={20} /> : <Menu size={20} />}
          </button>
        </div>

        {/* Mobile Nav Menu */}
        {mobileMenuOpen && (
          <div className="md:hidden absolute top-full left-0 right-0 bg-white border-b border-slate-200/80 p-4 space-y-3 flex flex-col items-stretch shadow-lg text-left">
            <button onClick={() => handleNavClick('how-it-works')} className="px-4 py-2 hover:bg-slate-50 rounded-lg font-medium text-slate-700 text-left">Cara Kerja</button>
            <button onClick={() => handleNavClick('services')} className="px-4 py-2 hover:bg-slate-50 rounded-lg font-medium text-slate-700 text-left">Layanan</button>
            <button onClick={() => handleNavClick('helper')} className="px-4 py-2 hover:bg-slate-50 rounded-lg font-medium text-slate-700 text-left">Gabung Helper</button>
            <button onClick={() => handleNavClick('faq')} className="px-4 py-2 hover:bg-slate-50 rounded-lg font-medium text-slate-700 text-left">FAQ</button>
            <div className="border-t border-slate-100 pt-3">
              <button 
                onClick={handleCtaClick}
                className="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold h-10 px-4 rounded-lg shadow-sm transition-all cursor-pointer"
              >
                <span>{isAuthenticated ? 'Ke Dashboard' : 'Mulai Sekarang'}</span>
                <ArrowRight size={14} />
              </button>
            </div>
          </div>
        )}
      </header>

      {/* Main Content Area */}
      <main className="flex-1">
        <HeroSection />
        
        <PlatformPreviewSection />
        
        <div id="how-it-works">
          <HowItWorksSection />
        </div>
        
        <div id="services">
          <PopularServicesSection />
        </div>
        
        <FeaturesSection />
        
        <div id="helper">
          <HelperSection />
        </div>
        
        <StatsSection />
        
        <TestimonialsSection />
        
        <div id="faq">
          <FAQSection />
        </div>
        
        <CTASection />
      </main>

      {/* Footer */}
      <FooterSection />
    </div>
  )
}
