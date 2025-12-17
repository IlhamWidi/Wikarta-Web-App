import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { CloudIcon, WifiIcon, BoltIcon, ShieldCheckIcon, CurrencyDollarIcon, UserGroupIcon, ChevronDownIcon, Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline';
import { motion, AnimatePresence } from 'framer-motion';

export default function LandingPage() {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    // Add smooth scroll behavior
    useEffect(() => {
        document.documentElement.style.scrollBehavior = 'smooth';
        return () => {
            document.documentElement.style.scrollBehavior = 'auto';
        };
    }, []);

    // Smooth scroll handler for navigation
    const handleScroll = (e, targetId) => {
        e.preventDefault();
        const element = document.getElementById(targetId);
        if (element) {
            const offsetTop = element.offsetTop - 80; // Account for fixed header
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
        setMobileMenuOpen(false); // Close mobile menu after click
    };

    // WhatsApp contact
    const whatsappNumber = '6281234567890';
    const whatsappMessage = 'Halo, saya tertarik dengan paket internet Wikarta. Bisa tolong info lebih lanjut?';
    const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}`;

    const features = [
        {
            icon: WifiIcon,
            title: 'Internet Super Cepat',
            description: 'Kecepatan hingga 100 Mbps untuk streaming dan gaming tanpa lag'
        },
        {
            icon: BoltIcon,
            title: 'Instalasi Gratis',
            description: 'Pemasangan cepat tanpa biaya tambahan'
        },
        {
            icon: ShieldCheckIcon,
            title: 'Koneksi Stabil',
            description: 'Uptime 99.9% dengan dukungan 24/7'
        },
        {
            icon: CurrencyDollarIcon,
            title: 'Harga Terjangkau',
            description: 'Paket mulai dari 100rb/bulan'
        },
        {
            icon: UserGroupIcon,
            title: 'Customer Support',
            description: 'Tim support siap membantu kapan saja'
        },
        {
            icon: CloudIcon,
            title: 'Cloud Storage',
            description: 'Bonus cloud storage untuk pelanggan setia'
        },
    ];

    const packages = [
        {
            name: 'Basic',
            speed: '20 Mbps',
            price: '100.000',
            features: ['Unlimited Kuota', 'Support 24/7', '1 Bulan Gratis']
        },
        {
            name: 'Premium',
            speed: '50 Mbps',
            price: '200.000',
            features: ['Unlimited Kuota', 'Support 24/7', 'Cloud Storage 10GB', '2 Bulan Gratis'],
            popular: true
        },
        {
            name: 'Ultra',
            speed: '100 Mbps',
            price: '350.000',
            features: ['Unlimited Kuota', 'Priority Support', 'Cloud Storage 50GB', '3 Bulan Gratis']
        },
    ];

    return (
        <div className="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
            {/* Navigation */}
            <nav className="relative z-50 backdrop-blur-xl bg-white/10 border-b border-white/10 sticky top-0">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-20">
                        <div className="flex items-center space-x-3">
                            <div className="relative">
                                <div className="absolute inset-0 bg-blue-500 blur-xl opacity-50"></div>
                                <WifiIcon className="relative h-10 w-10 text-white" />
                            </div>
                            <span className="text-3xl font-bold text-white tracking-tight">
                                Wikarta
                            </span>
                        </div>
                        
                        {/* Desktop Menu */}
                        <div className="hidden md:flex space-x-8">
                            <a href="#features" onClick={(e) => handleScroll(e, 'features')} className="text-white/80 hover:text-white transition text-lg cursor-pointer">Fitur</a>
                            <a href="#packages" onClick={(e) => handleScroll(e, 'packages')} className="text-white/80 hover:text-white transition text-lg cursor-pointer">Paket</a>
                            <a href="#contact" onClick={(e) => handleScroll(e, 'contact')} className="text-white/80 hover:text-white transition text-lg cursor-pointer">Kontak</a>
                        </div>
                        
                        <div className="flex items-center space-x-4">
                            <Link to="/login" className="hidden sm:block bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-8 py-3 rounded-full hover:shadow-2xl hover:shadow-blue-500/50 transform hover:scale-105 transition font-semibold">
                                Masuk
                            </Link>
                            
                            {/* Mobile Menu Button */}
                            <button
                                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                                className="md:hidden text-white p-2"
                            >
                                {mobileMenuOpen ? (
                                    <XMarkIcon className="h-8 w-8" />
                                ) : (
                                    <Bars3Icon className="h-8 w-8" />
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile Menu */}
                <AnimatePresence>
                    {mobileMenuOpen && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            className="md:hidden backdrop-blur-xl bg-slate-900/95 border-t border-white/10"
                        >
                            <div className="px-4 py-6 space-y-4">
                                <a 
                                    href="#features" 
                                    onClick={(e) => handleScroll(e, 'features')} 
                                    className="block text-white/80 hover:text-white transition text-lg py-2"
                                >
                                    Fitur
                                </a>
                                <a 
                                    href="#packages" 
                                    onClick={(e) => handleScroll(e, 'packages')} 
                                    className="block text-white/80 hover:text-white transition text-lg py-2"
                                >
                                    Paket
                                </a>
                                <a 
                                    href="#contact" 
                                    onClick={(e) => handleScroll(e, 'contact')} 
                                    className="block text-white/80 hover:text-white transition text-lg py-2"
                                >
                                    Kontak
                                </a>
                                <Link 
                                    to="/login" 
                                    className="block bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-8 py-3 rounded-full text-center font-semibold mt-4"
                                >
                                    Masuk
                                </Link>
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </nav>

            {/* Hero Section with Background Image */}
            <section className="relative min-h-screen flex items-center justify-center overflow-hidden">
                {/* Background Image with Overlay */}
                <div className="absolute inset-0 z-0">
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-slate-900/90 to-purple-900/95 z-10"></div>
                    <img 
                        src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=2000&auto=format&fit=crop" 
                        alt="Network Background"
                        className="w-full h-full object-cover opacity-30"
                    />
                    {/* Animated Network Lines Effect */}
                    <div className="absolute inset-0 z-20">
                        <div className="absolute w-96 h-96 bg-blue-500/20 rounded-full blur-3xl top-20 left-20 animate-pulse"></div>
                        <div className="absolute w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl bottom-20 right-20 animate-pulse" style={{ animationDelay: '1s' }}></div>
                        <div className="absolute w-96 h-96 bg-purple-500/20 rounded-full blur-3xl top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-pulse" style={{ animationDelay: '2s' }}></div>
                    </div>
                </div>

                <div className="relative z-30 max-w-7xl mx-auto px-4 py-32 text-center">
                    <motion.div
                        initial={{ opacity: 0, y: 30 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 1 }}
                    >
                        {/* Badge */}
                        <motion.div
                            initial={{ opacity: 0, scale: 0.5 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.5 }}
                            className="inline-block mb-6"
                        >
                            <span className="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 backdrop-blur-xl border border-blue-400/30 text-blue-300 px-6 py-2 rounded-full text-sm font-semibold">
                                ⚡ Provider Internet Terpercaya #1
                            </span>
                        </motion.div>

                        {/* Main Heading */}
                        <h1 className="text-6xl md:text-8xl font-black mb-8 leading-tight">
                            <span className="bg-gradient-to-r from-white via-blue-100 to-cyan-200 bg-clip-text text-transparent drop-shadow-2xl">
                                Internet Cepat &
                            </span>
                            <br />
                            <span className="bg-gradient-to-r from-cyan-200 via-blue-300 to-purple-300 bg-clip-text text-transparent drop-shadow-2xl">
                                Terpercaya
                            </span>
                        </h1>

                        <p className="text-xl md:text-2xl text-blue-100 mb-12 max-w-3xl mx-auto leading-relaxed">
                            Nikmati kecepatan internet tanpa batas dengan harga terjangkau. 
                            Cocok untuk <span className="text-cyan-300 font-semibold">streaming</span>, <span className="text-cyan-300 font-semibold">gaming</span>, dan <span className="text-cyan-300 font-semibold">bekerja dari rumah</span>.
                        </p>

                        {/* CTA Buttons */}
                        <div className="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                            <motion.a
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                href={whatsappLink}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="group bg-gradient-to-r from-green-500 to-emerald-600 text-white px-10 py-5 rounded-full text-lg font-bold hover:shadow-2xl hover:shadow-green-500/50 transition inline-flex items-center justify-center"
                            >
                                <svg className="w-7 h-7 mr-3 group-hover:rotate-12 transition" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Hubungi via WhatsApp
                            </motion.a>
                            <motion.a
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                href="#packages"
                                onClick={(e) => handleScroll(e, 'packages')}
                                className="backdrop-blur-xl bg-white/10 border-2 border-white/30 text-white px-10 py-5 rounded-full text-lg font-bold hover:bg-white/20 transition cursor-pointer"
                            >
                                Lihat Paket
                            </motion.a>
                        </div>

                        {/* Stats Card */}
                        <motion.div 
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 1, delay: 0.3 }}
                            className="max-w-5xl mx-auto"
                        >
                            <div className="backdrop-blur-2xl bg-white/10 border border-white/20 rounded-3xl p-8 md:p-12 shadow-2xl">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                                    <motion.div 
                                        whileHover={{ scale: 1.05 }}
                                        className="text-center"
                                    >
                                        <div className="text-6xl md:text-7xl font-black bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">
                                            10K+
                                        </div>
                                        <div className="text-blue-200 text-lg font-medium">Pelanggan Aktif</div>
                                    </motion.div>
                                    <motion.div 
                                        whileHover={{ scale: 1.05 }}
                                        className="text-center border-x border-white/10"
                                    >
                                        <div className="text-6xl md:text-7xl font-black bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                                            99.9%
                                        </div>
                                        <div className="text-blue-200 text-lg font-medium">Uptime</div>
                                    </motion.div>
                                    <motion.div 
                                        whileHover={{ scale: 1.05 }}
                                        className="text-center"
                                    >
                                        <div className="text-6xl md:text-7xl font-black bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                                            24/7
                                        </div>
                                        <div className="text-blue-200 text-lg font-medium">Support</div>
                                    </motion.div>
                                </div>
                            </div>
                        </motion.div>

                        {/* Scroll Indicator */}
                        <motion.div
                            animate={{ y: [0, 10, 0] }}
                            transition={{ duration: 2, repeat: Infinity }}
                            className="mt-16"
                        >
                            <ChevronDownIcon className="h-8 w-8 text-white/50 mx-auto" />
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* Features Section */}
            <section id="features" className="relative z-10 py-32 px-4 bg-gradient-to-b from-slate-900 to-blue-900">
                <div className="max-w-7xl mx-auto">
                    <div className="text-center mb-20">
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.8 }}
                            viewport={{ once: true }}
                        >
                            <span className="text-cyan-400 font-semibold text-lg mb-4 block">KEUNGGULAN KAMI</span>
                            <h2 className="text-5xl md:text-6xl font-black mb-6 text-white">
                                Kenapa Pilih Kami?
                            </h2>
                            <p className="text-blue-200 text-xl max-w-2xl mx-auto">
                                Layanan terbaik dengan teknologi terkini untuk pengalaman internet yang luar biasa
                            </p>
                        </motion.div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {features.map((feature, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.5, delay: index * 0.1 }}
                                viewport={{ once: true }}
                                whileHover={{ y: -10 }}
                                className="group backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 hover:border-cyan-500/50 transition-all duration-300"
                            >
                                <div className="relative mb-6">
                                    <div className="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-500 blur-xl opacity-0 group-hover:opacity-50 transition-opacity"></div>
                                    <feature.icon className="relative h-16 w-16 text-cyan-400 group-hover:text-cyan-300 transition-colors" />
                                </div>
                                <h3 className="text-2xl font-bold text-white mb-3">
                                    {feature.title}
                                </h3>
                                <p className="text-blue-200 text-lg leading-relaxed">
                                    {feature.description}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Packages Section */}
            <section id="packages" className="relative py-32 px-4 overflow-hidden">
                {/* Background with Network Image */}
                <div className="absolute inset-0 z-0">
                    <div className="absolute inset-0 bg-gradient-to-br from-slate-900/95 via-blue-900/95 to-purple-900/95 z-10"></div>
                    <img 
                        src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2000&auto=format&fit=crop" 
                        alt="Technology Background"
                        className="w-full h-full object-cover opacity-20"
                    />
                </div>

                <div className="relative z-20 max-w-7xl mx-auto">
                    <div className="text-center mb-20">
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.8 }}
                            viewport={{ once: true }}
                        >
                            <span className="text-cyan-400 font-semibold text-lg mb-4 block">PAKET PILIHAN</span>
                            <h2 className="text-5xl md:text-6xl font-black mb-6 text-white">
                                Paket Internet
                            </h2>
                            <p className="text-blue-200 text-xl max-w-2xl mx-auto">
                                Pilih paket yang sesuai dengan kebutuhan Anda dan nikmati kecepatan maksimal
                            </p>
                        </motion.div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {packages.map((pkg, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.5, delay: index * 0.15 }}
                                viewport={{ once: true }}
                                whileHover={{ y: -15, scale: 1.02 }}
                                className={`relative backdrop-blur-2xl ${
                                    pkg.popular 
                                        ? 'bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border-2 border-cyan-400/50 shadow-2xl shadow-cyan-500/20' 
                                        : 'bg-white/5 border border-white/10'
                                } rounded-3xl p-10 transition-all duration-300`}
                            >
                                {pkg.popular && (
                                    <div className="absolute -top-5 left-1/2 transform -translate-x-1/2">
                                        <span className="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2 rounded-full text-sm font-bold shadow-lg">
                                            ⭐ PALING POPULER
                                        </span>
                                    </div>
                                )}
                                <div className="text-center">
                                    <h3 className="text-3xl font-bold text-white mb-4">{pkg.name}</h3>
                                    <div className="mb-6">
                                        <div className="text-6xl font-black bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">
                                            {pkg.speed}
                                        </div>
                                        <div className="h-1 w-24 bg-gradient-to-r from-cyan-400 to-blue-400 mx-auto rounded-full"></div>
                                    </div>
                                    <div className="mb-8">
                                        <span className="text-2xl text-blue-200">Rp</span>
                                        <span className="text-5xl font-black text-white"> {pkg.price}</span>
                                        <span className="text-xl text-blue-200">/bulan</span>
                                    </div>
                                    <ul className="text-left space-y-4 mb-10">
                                        {pkg.features.map((feature, i) => (
                                            <li key={i} className="flex items-center text-blue-100">
                                                <svg className="h-6 w-6 text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                </svg>
                                                <span className="text-lg">{feature}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    <motion.a
                                        whileHover={{ scale: 1.05 }}
                                        whileTap={{ scale: 0.95 }}
                                        href={whatsappLink}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className={`block w-full ${
                                            pkg.popular 
                                                ? 'bg-gradient-to-r from-cyan-500 to-blue-500 shadow-lg shadow-cyan-500/50' 
                                                : 'bg-white/10 hover:bg-white/20'
                                        } text-white py-4 rounded-full font-bold text-lg transition-all`}
                                    >
                                        Pilih Paket Ini
                                    </motion.a>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Contact/Footer Section */}
            <footer id="contact" className="relative z-10 backdrop-blur-xl bg-slate-900/90 border-t border-white/10 py-16 px-4">
                <div className="max-w-7xl mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                        {/* Brand */}
                        <div>
                            <div className="flex items-center space-x-3 mb-4">
                                <WifiIcon className="h-10 w-10 text-cyan-400" />
                                <span className="text-3xl font-bold text-white">
                                    Wikarta
                                </span>
                            </div>
                            <p className="text-blue-200 mb-4">
                                Provider internet terpercaya dengan layanan terbaik untuk kebutuhan digital Anda.
                            </p>
                        </div>

                        {/* Quick Links */}
                        <div>
                            <h4 className="text-white font-bold text-lg mb-4">Quick Links</h4>
                            <ul className="space-y-2">
                                <li><a href="#features" onClick={(e) => handleScroll(e, 'features')} className="text-blue-200 hover:text-cyan-400 transition cursor-pointer">Fitur</a></li>
                                <li><a href="#packages" onClick={(e) => handleScroll(e, 'packages')} className="text-blue-200 hover:text-cyan-400 transition cursor-pointer">Paket</a></li>
                                <li><a href="#contact" onClick={(e) => handleScroll(e, 'contact')} className="text-blue-200 hover:text-cyan-400 transition cursor-pointer">Tentang Kami</a></li>
                                <li><a href="#contact" onClick={(e) => handleScroll(e, 'contact')} className="text-blue-200 hover:text-cyan-400 transition cursor-pointer">Kontak</a></li>
                            </ul>
                        </div>

                        {/* Contact */}
                        <div>
                            <h4 className="text-white font-bold text-lg mb-4">Hubungi Kami</h4>
                            <ul className="space-y-2 text-blue-200">
                                <li>📧 info@wikarta.com</li>
                                <li>📱 +62 812-3456-7890</li>
                                <li>📍 Jakarta, Indonesia</li>
                            </ul>
                        </div>
                    </div>

                    <div className="border-t border-white/10 pt-8 text-center">
                        <div className="flex justify-center space-x-6 mb-6">
                            <a href="#" className="text-blue-200 hover:text-cyan-400 transition">
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" className="text-blue-200 hover:text-cyan-400 transition">
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/></svg>
                            </a>
                            <a href="#" className="text-blue-200 hover:text-cyan-400 transition">
                                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                        <p className="text-blue-300 text-sm">
                            © 2025 Wikarta Provider. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
