import React from 'react';
import { Link } from 'react-router-dom';
import { CloudIcon, WifiIcon, BoltIcon, ShieldCheckIcon, CurrencyDollarIcon, UserGroupIcon } from '@heroicons/react/24/outline';
import { motion } from 'framer-motion';

export default function LandingPage() {
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
        <div className="relative min-h-screen overflow-hidden">
            {/* Animated Cloud Background */}
            <div className="absolute inset-0 overflow-hidden">
                <div className="absolute w-96 h-96 bg-blue-200/30 rounded-full blur-3xl -top-20 -left-20 animate-pulse"></div>
                <div className="absolute w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl top-40 right-20 animate-pulse delay-1000"></div>
                <div className="absolute w-96 h-96 bg-sky-200/30 rounded-full blur-3xl bottom-20 left-1/3 animate-pulse delay-2000"></div>
            </div>

            {/* Navigation */}
            <nav className="relative z-10 backdrop-blur-md bg-white/70 border-b border-white/20 sticky top-0">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-16">
                        <div className="flex items-center space-x-2">
                            <CloudIcon className="h-8 w-8 text-blue-600" />
                            <span className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                Wikarta
                            </span>
                        </div>
                        <div className="hidden md:flex space-x-8">
                            <a href="#features" className="text-gray-700 hover:text-blue-600 transition">Fitur</a>
                            <a href="#packages" className="text-gray-700 hover:text-blue-600 transition">Paket</a>
                            <a href="#contact" className="text-gray-700 hover:text-blue-600 transition">Kontak</a>
                        </div>
                        <div className="flex space-x-4">
                            <Link to="/login" className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-full hover:shadow-lg transform hover:scale-105 transition">
                                Masuk
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Hero Section */}
            <section className="relative z-10 pt-20 pb-32 px-4">
                <div className="max-w-7xl mx-auto text-center">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.8 }}
                    >
                        <h1 className="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Internet Cepat & Terpercaya
                        </h1>
                        <p className="text-xl md:text-2xl text-gray-700 mb-8 max-w-3xl mx-auto">
                            Nikmati kecepatan internet tanpa batas dengan harga terjangkau. 
                            Cocok untuk streaming, gaming, dan bekerja dari rumah.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <a 
                                href="https://wa.me/6281234567890" 
                                target="_blank"
                                rel="noopener noreferrer"
                                className="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition inline-flex items-center justify-center"
                            >
                                <svg className="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Hubungi via WhatsApp
                            </a>
                        </div>
                    </motion.div>

                    {/* Glassmorphism Hero Card */}
                    <motion.div 
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 1, delay: 0.3 }}
                        className="mt-16 max-w-4xl mx-auto"
                    >
                        <div className="backdrop-blur-lg bg-white/30 border border-white/40 rounded-3xl p-8 shadow-2xl">
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div className="text-center">
                                    <div className="text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                        10K+
                                    </div>
                                    <div className="text-gray-700 mt-2">Pelanggan Aktif</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                        99.9%
                                    </div>
                                    <div className="text-gray-700 mt-2">Uptime</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                        24/7
                                    </div>
                                    <div className="text-gray-700 mt-2">Support</div>
                                </div>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </section>

            {/* Features Section */}
            <section id="features" className="relative z-10 py-20 px-4">
                <div className="max-w-7xl mx-auto">
                    <div className="text-center mb-16">
                        <h2 className="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Kenapa Pilih Kami?
                        </h2>
                        <p className="text-gray-700 text-lg">
                            Layanan terbaik dengan teknologi terkini
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {features.map((feature, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.5, delay: index * 0.1 }}
                                viewport={{ once: true }}
                                className="backdrop-blur-lg bg-white/40 border border-white/50 rounded-2xl p-6 hover:shadow-2xl transform hover:scale-105 transition"
                            >
                                <feature.icon className="h-12 w-12 text-blue-600 mb-4" />
                                <h3 className="text-xl font-semibold text-gray-800 mb-2">
                                    {feature.title}
                                </h3>
                                <p className="text-gray-600">
                                    {feature.description}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Packages Section */}
            <section id="packages" className="relative z-10 py-20 px-4">
                <div className="max-w-7xl mx-auto">
                    <div className="text-center mb-16">
                        <h2 className="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Paket Internet
                        </h2>
                        <p className="text-gray-700 text-lg">
                            Pilih paket yang sesuai dengan kebutuhan Anda
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {packages.map((pkg, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, scale: 0.9 }}
                                whileInView={{ opacity: 1, scale: 1 }}
                                transition={{ duration: 0.5, delay: index * 0.1 }}
                                viewport={{ once: true }}
                                className={`backdrop-blur-lg ${pkg.popular ? 'bg-gradient-to-br from-blue-500/30 to-indigo-500/30 border-2 border-blue-500' : 'bg-white/40 border border-white/50'} rounded-3xl p-8 hover:shadow-2xl transform hover:scale-105 transition relative`}
                            >
                                {pkg.popular && (
                                    <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                        <span className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                            Paling Populer
                                        </span>
                                    </div>
                                )}
                                <div className="text-center">
                                    <h3 className="text-2xl font-bold text-gray-800 mb-2">{pkg.name}</h3>
                                    <div className="text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                                        {pkg.speed}
                                    </div>
                                    <div className="text-3xl font-semibold text-gray-800 mb-6">
                                        Rp {pkg.price}<span className="text-lg text-gray-600">/bulan</span>
                                    </div>
                                    <ul className="text-left space-y-3 mb-8">
                                        {pkg.features.map((feature, i) => (
                                            <li key={i} className="flex items-center text-gray-700">
                                                <svg className="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                </svg>
                                                {feature}
                                            </li>
                                        ))}
                                    </ul>
                                    <a
                                        href="https://wa.me/6281234567890"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className={`block w-full ${pkg.popular ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-gray-800'} text-white py-3 rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition`}
                                    >
                                        Pilih Paket
                                    </a>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="relative z-10 backdrop-blur-md bg-white/70 border-t border-white/20 py-12 px-4">
                <div className="max-w-7xl mx-auto text-center">
                    <div className="flex items-center justify-center space-x-2 mb-4">
                        <CloudIcon className="h-8 w-8 text-blue-600" />
                        <span className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Wikarta
                        </span>
                    </div>
                    <p className="text-gray-600 mb-4">
                        Internet Cepat & Terpercaya untuk Semua
                    </p>
                    <div className="flex justify-center space-x-6 mb-4">
                        <a href="#" className="text-gray-600 hover:text-blue-600 transition">Facebook</a>
                        <a href="#" className="text-gray-600 hover:text-blue-600 transition">Instagram</a>
                        <a href="#" className="text-gray-600 hover:text-blue-600 transition">Twitter</a>
                    </div>
                    <p className="text-gray-500 text-sm">
                        © 2025 Wikarta. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    );
}
