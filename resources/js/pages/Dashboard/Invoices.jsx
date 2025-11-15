import { useState, useEffect } from 'react';
import { MagnifyingGlassIcon, DocumentTextIcon, XCircleIcon, ArrowPathIcon } from '@heroicons/react/24/outline';
import DashboardLayout from '../../layouts/DashboardLayout';
import api from '../../utils/api';
import { format } from 'date-fns';
import toast from 'react-hot-toast';

export default function Invoices() {
    const [invoices, setInvoices] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('all');
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [selectedInvoice, setSelectedInvoice] = useState(null);

    useEffect(() => {
        fetchInvoices();
    }, []);

    const fetchInvoices = async () => {
        try {
            setLoading(true);
            const response = await api.get('/invoices');
            setInvoices(response.data.data);
        } catch (error) {
            console.error('Failed to fetch invoices:', error);
        } finally {
            setLoading(false);
        }
    };

    const requestReason = (message) => {
        const reason = prompt(message);
        if (!reason || !reason.trim()) {
            toast.error('Alasan wajib diisi untuk melanjutkan');
            return null;
        }
        return reason.trim();
    };

    const handleVoid = async (id) => {
        if (!confirm('Yakin ingin membatalkan invoice ini?')) return;
        const reason = requestReason('Masukkan alasan pembatalan invoice:');
        if (!reason) return;

        try {
            await api.post(`/invoices/${id}/void`, { void_reason: reason });
            fetchInvoices();
            toast.success('Invoice berhasil dibatalkan');
        } catch (error) {
            toast.error('Gagal membatalkan invoice');
        }
    };

    const handleRefund = async (id) => {
        if (!confirm('Yakin ingin refund invoice ini?')) return;
        const reason = requestReason('Masukkan alasan refund invoice:');
        if (!reason) return;

        try {
            await api.post(`/invoices/${id}/refund`, { refund_reason: reason });
            fetchInvoices();
            toast.success('Refund berhasil diproses');
        } catch (error) {
            toast.error('Gagal memproses refund');
        }
    };

    const viewDetail = async (invoice) => {
        try {
            const response = await api.get(`/invoices/${invoice.id}`);
            setSelectedInvoice(response.data.data);
            setShowDetailModal(true);
        } catch (error) {
            toast.error('Gagal memuat detail invoice');
        }
    };

    const filteredInvoices = invoices.filter(invoice => {
        const matchesSearch = invoice.invoice_number.toLowerCase().includes(search.toLowerCase()) ||
                            invoice.customer?.name.toLowerCase().includes(search.toLowerCase());
        const matchesStatus = statusFilter === 'all' || invoice.status === statusFilter;
        return matchesSearch && matchesStatus;
    });

    const statusColors = {
        draft: 'bg-gray-100 text-gray-800',
        issued: 'bg-blue-100 text-blue-800',
        pending: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        overdue: 'bg-red-100 text-red-800',
        void: 'bg-gray-100 text-gray-800',
        refund: 'bg-purple-100 text-purple-800'
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
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">Invoices</h1>
                        <p className="text-gray-600">Kelola invoice dan tagihan pelanggan</p>
                    </div>

                {/* Filters */}
                <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 mb-6">
                    <div className="flex flex-col md:flex-row gap-4 items-center">
                        <div className="relative flex-1">
                            <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Cari nomor invoice atau nama customer..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            />
                        </div>

                        <select
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="all">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="issued">Issued</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                            <option value="void">Void</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>

                    <div className="mt-4 text-sm text-gray-600">
                        Menampilkan {filteredInvoices.length} dari {invoices.length} invoices
                    </div>
                </div>

                {/* Table */}
                <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                    {loading ? (
                        <div className="p-12 text-center">
                            <div className="inline-block w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
                            <p className="mt-4 text-gray-600">Loading...</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gradient-to-r from-purple-600 to-pink-600 text-white">
                                    <tr>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">No. Invoice</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Customer</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Jatuh Tempo</th>
                                        <th className="px-6 py-4 text-right text-sm font-semibold">Total</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Status</th>
                                        <th className="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {filteredInvoices.length === 0 ? (
                                        <tr>
                                            <td colSpan="7" className="px-6 py-12 text-center text-gray-500">
                                                Tidak ada data invoice
                                            </td>
                                        </tr>
                                    ) : (
                                        filteredInvoices.map((invoice) => (
                                            <tr key={invoice.id} className="hover:bg-purple-50/50 transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="font-medium text-gray-900">{invoice.invoice_number}</div>
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-600">
                                                    {invoice.customer?.name || '-'}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-600">
                                                    {format(new Date(invoice.invoice_date), 'dd MMM yyyy')}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-600">
                                                    {format(new Date(invoice.due_date), 'dd MMM yyyy')}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                                    {formatCurrency(invoice.total)}
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className={`px-3 py-1 rounded-full text-xs font-medium ${statusColors[invoice.status]}`}>
                                                        {invoice.status}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center justify-center gap-2">
                                                        <button
                                                            onClick={() => viewDetail(invoice)}
                                                            className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                            title="Detail"
                                                        >
                                                            <DocumentTextIcon className="w-5 h-5" />
                                                        </button>
                                                        {invoice.status === 'pending' && (
                                                            <button
                                                                onClick={() => handleVoid(invoice.id)}
                                                                className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                                title="Void"
                                                            >
                                                                <XCircleIcon className="w-5 h-5" />
                                                            </button>
                                                        )}
                                                        {invoice.status === 'paid' && (
                                                            <button
                                                                onClick={() => handleRefund(invoice.id)}
                                                                className="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                                                title="Refund"
                                                            >
                                                                <ArrowPathIcon className="w-5 h-5" />
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>

            {/* Detail Modal */}
            {showDetailModal && selectedInvoice && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                        <div className="sticky top-0 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4 flex items-center justify-between">
                            <h2 className="text-xl font-bold">Detail Invoice</h2>
                            <button
                                onClick={() => setShowDetailModal(false)}
                                className="p-1 hover:bg-white/20 rounded-lg transition-colors"
                            >
                                <span className="text-2xl">&times;</span>
                            </button>
                        </div>

                        <div className="p-6">
                            {/* Invoice Header */}
                            <div className="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">No. Invoice</p>
                                    <p className="font-bold text-lg">{selectedInvoice.invoice_number}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Status</p>
                                    <span className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${statusColors[selectedInvoice.status]}`}>
                                        {selectedInvoice.status}
                                    </span>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Customer</p>
                                    <p className="font-medium">{selectedInvoice.customer?.name}</p>
                                    <p className="text-sm text-gray-600">{selectedInvoice.customer?.email}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Tanggal Invoice</p>
                                    <p className="font-medium">{format(new Date(selectedInvoice.invoice_date), 'dd MMMM yyyy')}</p>
                                    <p className="text-sm text-gray-600">Jatuh tempo: {format(new Date(selectedInvoice.due_date), 'dd MMMM yyyy')}</p>
                                </div>
                            </div>

                            {/* Invoice Items */}
                            <div className="mb-6">
                                <h3 className="font-bold text-lg mb-4">Item Invoice</h3>
                                <table className="w-full">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-4 py-2 text-left text-sm font-semibold text-gray-700">Deskripsi</th>
                                            <th className="px-4 py-2 text-center text-sm font-semibold text-gray-700">Qty</th>
                                            <th className="px-4 py-2 text-right text-sm font-semibold text-gray-700">Harga</th>
                                            <th className="px-4 py-2 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200">
                                        {selectedInvoice.items?.map((item, index) => (
                                            <tr key={index}>
                                                <td className="px-4 py-3 text-sm">{item.description}</td>
                                                <td className="px-4 py-3 text-sm text-center">{item.quantity}</td>
                                                <td className="px-4 py-3 text-sm text-right">{formatCurrency(item.unit_price)}</td>
                                                <td className="px-4 py-3 text-sm text-right font-medium">{formatCurrency(item.subtotal)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Totals */}
                            <div className="bg-gray-50 rounded-lg p-4">
                                <div className="flex justify-between mb-2">
                                    <span className="text-gray-600">Subtotal</span>
                                    <span className="font-medium">{formatCurrency(selectedInvoice.subtotal)}</span>
                                </div>
                                {selectedInvoice.tax > 0 && (
                                    <div className="flex justify-between mb-2">
                                        <span className="text-gray-600">Pajak</span>
                                        <span className="font-medium">{formatCurrency(selectedInvoice.tax)}</span>
                                    </div>
                                )}
                                {selectedInvoice.discount > 0 && (
                                    <div className="flex justify-between mb-2">
                                        <span className="text-gray-600">Diskon</span>
                                        <span className="font-medium text-red-600">-{formatCurrency(selectedInvoice.discount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between pt-2 border-t-2 border-gray-300">
                                    <span className="font-bold text-lg">Total</span>
                                    <span className="font-bold text-lg text-purple-600">{formatCurrency(selectedInvoice.total)}</span>
                                </div>
                            </div>

                            {selectedInvoice.notes && (
                                <div className="mt-4 p-4 bg-blue-50 rounded-lg">
                                    <p className="text-sm font-medium text-gray-700 mb-1">Catatan:</p>
                                    <p className="text-sm text-gray-600">{selectedInvoice.notes}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </div>
        </DashboardLayout>
    );
}
