import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../stores/authStore';
import api from '../utils/api';
import {
    HomeIcon,
    UserGroupIcon,
    CubeIcon,
    DocumentTextIcon,
    CreditCardIcon,
    TicketIcon,
    WrenchScrewdriverIcon,
    BellIcon,
    ShieldCheckIcon,
    ArrowLeftOnRectangleIcon,
    ShieldExclamationIcon,
} from '@heroicons/react/24/outline';

const menuItems = [
    { name: 'Dashboard', path: '/dashboard', icon: HomeIcon, permission: null },
    { name: 'Users & Roles', path: '/roles', icon: ShieldExclamationIcon, permission: 'manage_roles' },
    { name: 'Customers', path: '/dashboard/customers', icon: UserGroupIcon, permission: 'view_customers' },
    { name: 'Packages', path: '/dashboard/packages', icon: CubeIcon, permission: 'view_packages' },
    { name: 'Invoices', path: '/dashboard/invoices', icon: DocumentTextIcon, permission: 'view_invoices' },
    { name: 'Payments', path: '/dashboard/payments', icon: CreditCardIcon, permission: 'view_payments' },
    { name: 'Support Tickets', path: '/dashboard/tickets', icon: TicketIcon, permission: 'view_tickets' },
    { name: 'Installations', path: '/dashboard/installations', icon: WrenchScrewdriverIcon, permission: 'view_installations' },
    { name: 'Notifications', path: '/dashboard/notifications', icon: BellIcon, permission: 'view_reports' },
    { name: 'Audit Logs', path: '/dashboard/audit-logs', icon: ShieldCheckIcon, permission: 'view_audit_logs' },
];

export default function DashboardLayout({ children }) {
    const location = useLocation();
    const navigate = useNavigate();
    const { user, logout } = useAuthStore();

    const handleLogout = async () => {
        try {
            await api.post('/auth/logout');
        } catch (error) {
            console.error('Logout failed', error);
        } finally {
            logout();
            navigate('/login');
        }
    };

    // Check if user has permission
    const hasPermission = (permission) => {
        if (!permission) return true;
        if (!user) return false;
        const roleKey = (user.role || user.roles?.[0]?.name || '').toLowerCase();
        if (roleKey === 'superuser') return true;
        return user.permissions?.includes(permission);
    };

    return (
        <div className="min-h-screen bg-gray-100 flex">
            {/* Sidebar */}
            <aside className="w-64 bg-gradient-to-b from-blue-900 to-purple-900 text-white fixed h-full overflow-y-auto">
                {/* Logo */}
                <div className="p-6 border-b border-white/20">
                    <h1 className="text-2xl font-bold">Wikarta</h1>
                    <p className="text-sm text-blue-200 mt-1">Admin Dashboard</p>
                </div>

                {/* User Info */}
                <div className="p-6 border-b border-white/20">
                    <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <span className="text-lg font-bold">{user?.name?.[0]}</span>
                        </div>
                        <div>
                            <p className="font-medium">{user?.name}</p>
                            <p className="text-xs text-blue-200">{user?.role}</p>
                        </div>
                    </div>
                </div>

                {/* Menu */}
                <nav className="p-4 space-y-1">
                    {menuItems.map((item) => {
                        if (!hasPermission(item.permission)) return null;

                        const isActive = location.pathname === item.path;
                        const Icon = item.icon;

                        return (
                            <Link
                                key={item.path}
                                to={item.path}
                                className={`flex items-center space-x-3 px-4 py-3 rounded-lg transition ${
                                    isActive
                                        ? 'bg-white/20 text-white'
                                        : 'text-blue-200 hover:bg-white/10 hover:text-white'
                                }`}
                            >
                                <Icon className="w-5 h-5" />
                                <span>{item.name}</span>
                            </Link>
                        );
                    })}
                </nav>

                {/* Logout */}
                <div className="p-4 border-t border-white/20 absolute bottom-0 w-64 bg-gradient-to-b from-transparent to-purple-900">
                    <button
                        onClick={handleLogout}
                        className="flex items-center space-x-3 px-4 py-3 w-full rounded-lg text-red-200 hover:bg-red-500/20 transition"
                    >
                        <ArrowLeftOnRectangleIcon className="w-5 h-5" />
                        <span>Logout</span>
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 ml-64">
                {/* Header */}
                <header className="bg-white shadow-sm sticky top-0 z-10">
                    <div className="px-6 py-4">
                        <h2 className="text-2xl font-bold text-gray-800">
                            {menuItems.find(item => item.path === location.pathname)?.name || 'Dashboard'}
                        </h2>
                    </div>
                </header>

                {/* Page Content */}
                <main className="p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
