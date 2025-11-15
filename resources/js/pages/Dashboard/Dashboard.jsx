import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import DashboardLayout from '../../layouts/DashboardLayout';
import api from '../../utils/api';
import {
    UserGroupIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    CheckCircleIcon,
} from '@heroicons/react/24/outline';

export default function Dashboard() {
    const navigate = useNavigate();
    const [stats, setStats] = useState({
        totalCustomers: 0,
        totalRevenue: 0,
        pendingInvoices: 0,
        paidInvoices: 0,
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchStats();
    }, []);

    const fetchStats = async () => {
        setLoading(true);
        try {
            // Fetch statistics from API
            // For now, using placeholder data
            setStats({
                totalCustomers: 1250,
                totalRevenue: 125000000,
                pendingInvoices: 45,
                paidInvoices: 890,
            });
        } catch (error) {
            console.error('Failed to fetch stats:', error);
        } finally {
            setLoading(false);
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const statCards = [
        {
            title: 'Total Customers',
            value: stats.totalCustomers.toLocaleString(),
            icon: UserGroupIcon,
            color: 'blue',
        },
        {
            title: 'Total Revenue',
            value: formatCurrency(stats.totalRevenue),
            icon: CurrencyDollarIcon,
            color: 'green',
        },
        {
            title: 'Pending Invoices',
            value: stats.pendingInvoices.toLocaleString(),
            icon: DocumentTextIcon,
            color: 'yellow',
        },
        {
            title: 'Paid Invoices',
            value: stats.paidInvoices.toLocaleString(),
            icon: CheckCircleIcon,
            color: 'purple',
        },
    ];

    const colorClasses = {
        blue: 'from-blue-500 to-blue-600',
        green: 'from-green-500 to-green-600',
        yellow: 'from-yellow-500 to-yellow-600',
        purple: 'from-purple-500 to-purple-600',
    };

    return (
        <DashboardLayout>
            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {statCards.map((stat, index) => {
                    const Icon = stat.icon;
                    return (
                        <div key={index} className="bg-white rounded-lg shadow-md overflow-hidden">
                            <div className={`bg-gradient-to-r ${colorClasses[stat.color]} p-4`}>
                                <Icon className="w-8 h-8 text-white" />
                            </div>
                            <div className="p-6">
                                <p className="text-gray-500 text-sm mb-1">{stat.title}</p>
                                <p className="text-2xl font-bold text-gray-800">
                                    {loading ? '...' : stat.value}
                                </p>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Quick Actions */}
            <div className="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 className="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button 
                        onClick={() => navigate('/dashboard/customers')}
                        className="p-4 border-2 border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition"
                    >
                        + New Customer
                    </button>
                    <button 
                        onClick={() => navigate('/dashboard/invoices')}
                        className="p-4 border-2 border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition"
                    >
                        + Create Invoice
                    </button>
                    <button 
                        onClick={() => navigate('/dashboard/tickets')}
                        className="p-4 border-2 border-purple-500 text-purple-600 rounded-lg hover:bg-purple-50 transition"
                    >
                        + New Ticket
                    </button>
                </div>
            </div>

            {/* Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Recent Invoices */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">Recent Invoices</h3>
                    <div className="space-y-3">
                        {loading ? (
                            <p className="text-gray-500 text-center py-4">Loading...</p>
                        ) : (
                            <>
                                <div className="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <p className="font-medium text-gray-800">INV-2024-001</p>
                                        <p className="text-sm text-gray-500">Customer A</p>
                                    </div>
                                    <span className="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                        Pending
                                    </span>
                                </div>
                                <div className="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <p className="font-medium text-gray-800">INV-2024-002</p>
                                        <p className="text-sm text-gray-500">Customer B</p>
                                    </div>
                                    <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        Paid
                                    </span>
                                </div>
                            </>
                        )}
                    </div>
                </div>

                {/* Recent Tickets */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">Recent Support Tickets</h3>
                    <div className="space-y-3">
                        {loading ? (
                            <p className="text-gray-500 text-center py-4">Loading...</p>
                        ) : (
                            <>
                                <div className="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <p className="font-medium text-gray-800">Internet Lambat</p>
                                        <p className="text-sm text-gray-500">Ticket #1234</p>
                                    </div>
                                    <span className="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                        Open
                                    </span>
                                </div>
                                <div className="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <p className="font-medium text-gray-800">Gangguan Koneksi</p>
                                        <p className="text-sm text-gray-500">Ticket #1235</p>
                                    </div>
                                    <span className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        In Progress
                                    </span>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
