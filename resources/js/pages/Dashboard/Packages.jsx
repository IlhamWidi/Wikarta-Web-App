import { useState, useEffect } from 'react';
import { PlusIcon, PencilIcon, TrashIcon, WifiIcon, XMarkIcon } from '@heroicons/react/24/outline';
import DashboardLayout from '../../layouts/DashboardLayout';
import api from '../../utils/api';
import toast from 'react-hot-toast';

export default function Packages() {
    const [packages, setPackages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [currentPackage, setCurrentPackage] = useState(null);
    const [formData, setFormData] = useState({
        name: '',
        speed_mbps: '',
        price: '',
        quota_gb: '',
        features: [],
        description: ''
    });
    const [featureInput, setFeatureInput] = useState('');
    const [errors, setErrors] = useState({});

    useEffect(() => {
        fetchPackages();
    }, []);

    const fetchPackages = async () => {
        try {
            setLoading(true);
            const response = await api.get('/packages');
            setPackages(response.data.data);
        } catch (error) {
            console.error('Failed to fetch packages:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleCreate = () => {
        setEditMode(false);
        setCurrentPackage(null);
        setFormData({
            name: '',
            speed_mbps: '',
            price: '',
            quota_gb: '',
            features: [],
            description: ''
        });
        setErrors({});
        setShowModal(true);
    };

    const handleEdit = (pkg) => {
        setEditMode(true);
        setCurrentPackage(pkg);
        setFormData({
            name: pkg.name,
            speed_mbps: pkg.speed_mbps,
            price: pkg.price,
            quota_gb: pkg.quota_gb || '',
            features: pkg.features || [],
            description: pkg.description || ''
        });
        setErrors({});
        setShowModal(true);
    };

    const addFeature = () => {
        if (featureInput.trim()) {
            setFormData({
                ...formData,
                features: [...formData.features, featureInput.trim()]
            });
            setFeatureInput('');
        }
    };

    const removeFeature = (index) => {
        setFormData({
            ...formData,
            features: formData.features.filter((_, i) => i !== index)
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});

        try {
            if (editMode) {
                await api.put(`/packages/${currentPackage.id}`, formData);
            } else {
                await api.post('/packages', formData);
            }
            toast.success('Paket berhasil disimpan!');
            setShowModal(false);
            fetchPackages();
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                toast.error('Gagal menyimpan paket');
            }
        }
    };

    const handleDelete = async (id) => {
        if (!confirm('Yakin ingin menghapus paket ini?')) return;

        try {
            await api.delete(`/packages/${id}`);
            toast.success('Paket berhasil dihapus!');
            fetchPackages();
        } catch (error) {
            toast.error('Gagal menghapus paket');
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    };

    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 p-6">
                <div className="max-w-7xl mx-auto">
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">Internet Packages</h1>
                        <p className="text-gray-600">Kelola paket internet Agus Provider</p>
                    </div>

                {/* Action Bar */}
                <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 mb-6">
                    <div className="flex justify-between items-center">
                        <div className="text-sm text-gray-600">
                            Total {packages.length} paket tersedia
                        </div>
                        <button
                            onClick={handleCreate}
                            className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all"
                        >
                            <PlusIcon className="w-5 h-5" />
                            Tambah Paket Baru
                        </button>
                    </div>
                </div>

                {/* Packages Grid */}
                {loading ? (
                    <div className="p-12 text-center">
                        <div className="inline-block w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
                        <p className="mt-4 text-gray-600">Loading...</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {packages.map((pkg) => (
                            <div
                                key={pkg.id}
                                className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all"
                            >
                                {/* Package Header */}
                                <div className="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <WifiIcon className="w-10 h-10" />
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() => handleEdit(pkg)}
                                                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
                                            >
                                                <PencilIcon className="w-5 h-5" />
                                            </button>
                                            <button
                                                onClick={() => handleDelete(pkg.id)}
                                                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
                                            >
                                                <TrashIcon className="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                    <h3 className="text-2xl font-bold mb-2">{pkg.name}</h3>
                                    <p className="text-3xl font-bold">{pkg.speed_mbps} Mbps</p>
                                </div>

                                {/* Package Body */}
                                <div className="p-6">
                                    <div className="mb-6">
                                        <p className="text-3xl font-bold text-purple-600 mb-1">
                                            {formatCurrency(pkg.price)}
                                        </p>
                                        <p className="text-gray-500 text-sm">per bulan</p>
                                    </div>

                                    {pkg.quota_gb && (
                                        <div className="mb-4 p-3 bg-blue-50 rounded-lg">
                                            <p className="text-sm text-gray-600">Kuota</p>
                                            <p className="font-bold text-blue-600">{pkg.quota_gb} GB</p>
                                        </div>
                                    )}

                                    {pkg.features && pkg.features.length > 0 && (
                                        <div className="mb-4">
                                            <p className="font-semibold text-gray-700 mb-2">Fitur:</p>
                                            <ul className="space-y-2">
                                                {pkg.features.map((feature, index) => (
                                                    <li key={index} className="flex items-start gap-2 text-sm text-gray-600">
                                                        <span className="text-green-500 mt-1">✓</span>
                                                        {feature}
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    )}

                                    {pkg.description && (
                                        <p className="text-sm text-gray-600 italic">{pkg.description}</p>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Create/Edit Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div className="sticky top-0 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4 flex items-center justify-between">
                            <h2 className="text-xl font-bold">
                                {editMode ? 'Edit Paket' : 'Tambah Paket Baru'}
                            </h2>
                            <button
                                onClick={() => setShowModal(false)}
                                className="p-1 hover:bg-white/20 rounded-lg transition-colors"
                            >
                                <XMarkIcon className="w-6 h-6" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Paket *
                                </label>
                                <input
                                    type="text"
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    required
                                />
                                {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name[0]}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Kecepatan (Mbps) *
                                    </label>
                                    <input
                                        type="number"
                                        value={formData.speed_mbps}
                                        onChange={(e) => setFormData({ ...formData, speed_mbps: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required
                                    />
                                    {errors.speed_mbps && <p className="mt-1 text-sm text-red-600">{errors.speed_mbps[0]}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Harga per Bulan (Rp) *
                                    </label>
                                    <input
                                        type="number"
                                        value={formData.price}
                                        onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required
                                    />
                                    {errors.price && <p className="mt-1 text-sm text-red-600">{errors.price[0]}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Kuota (GB) - Opsional
                                </label>
                                <input
                                    type="number"
                                    value={formData.quota_gb}
                                    onChange={(e) => setFormData({ ...formData, quota_gb: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Kosongkan jika unlimited"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Fitur-fitur
                                </label>
                                <div className="flex gap-2 mb-2">
                                    <input
                                        type="text"
                                        value={featureInput}
                                        onChange={(e) => setFeatureInput(e.target.value)}
                                        onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addFeature())}
                                        className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="Tambahkan fitur..."
                                    />
                                    <button
                                        type="button"
                                        onClick={addFeature}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        Tambah
                                    </button>
                                </div>
                                <div className="space-y-2">
                                    {formData.features.map((feature, index) => (
                                        <div key={index} className="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                                            <span className="flex-1 text-sm">{feature}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeFeature(index)}
                                                className="text-red-600 hover:bg-red-50 p-1 rounded"
                                            >
                                                <XMarkIcon className="w-4 h-4" />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea
                                    value={formData.description}
                                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                    rows="3"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Deskripsi singkat paket..."
                                />
                            </div>

                            <div className="flex gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all"
                                >
                                    {editMode ? 'Update' : 'Simpan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
        </DashboardLayout>
    );
}
