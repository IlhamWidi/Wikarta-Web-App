import { useEffect, useState } from "react";
import {
    ArrowPathIcon,
    EnvelopeIcon,
    PaperAirplaneIcon,
    PhoneIcon,
    XMarkIcon,
} from "@heroicons/react/24/outline";
import DashboardLayout from "../../layouts/DashboardLayout";
import api from "../../utils/api";
import { useAuthStore } from "../../stores/authStore";

const statusBadge = {
    sent: "bg-emerald-100 text-emerald-700",
    pending: "bg-amber-100 text-amber-700",
    failed: "bg-red-100 text-red-700",
};
export default function Notifications() {
    const { user } = useAuthStore();
    const isSuperuser = (user?.role || "").toLowerCase() === "superuser";
    const canResend = isSuperuser || user?.permissions?.includes("view_reports");

    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filters, setFilters] = useState({ status: "all", channel: "all", search: "" });
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [selectedLog, setSelectedLog] = useState(null);

    useEffect(() => {
        fetchLogs();
    }, [page, filters]);

    const fetchLogs = async () => {
        setLoading(true);
        try {
            const params = { page, per_page: 15 };
            if (filters.status !== "all") params.status = filters.status;
            if (filters.channel !== "all") params.channel = filters.channel;
            if (filters.search) params.search = filters.search;

            const response = await api.get("/notifications/logs", { params });
            setLogs(response.data.data || []);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            });
        } catch (error) {
            console.error("Failed to fetch notification logs", error);
        } finally {
            setLoading(false);
        }
    };

    const handleResend = async (logId) => {
        if (!canResend) return;
        try {
            await api.post(`/notifications/${logId}/resend`);
            fetchLogs();
        } catch (error) {
            alert(error.response?.data?.message || "Gagal mengirim ulang notifikasi");
        }
    };
    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gradient-to-br from-violet-50 via-white to-slate-50 p-6">
                <div className="max-w-6xl mx-auto space-y-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">Notification Center</h1>
                            <p className="text-sm text-slate-500">Pantau pengiriman email/pesan dunning secara real-time.</p>
                        </div>
                        <button
                            onClick={fetchLogs}
                            className="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 text-sm text-slate-700"
                        >
                            <ArrowPathIcon className="w-4 h-4 mr-2" /> Refresh
                        </button>
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Status</label>
                                <select
                                    value={filters.status}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, status: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-violet-500 focus:border-violet-500"
                                >
                                    <option value="all">Semua</option>
                                    <option value="sent">Sent</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Channel</label>
                                <select
                                    value={filters.channel}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, channel: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-violet-500 focus:border-violet-500"
                                >
                                    <option value="all">Semua</option>
                                    <option value="email">Email</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="sms">SMS</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Search</label>
                                <div className="flex rounded-xl border border-slate-200 overflow-hidden">
                                    <input
                                        type="text"
                                        placeholder="Cari penerima atau subject"
                                        value={filters.search}
                                        onChange={(e) => setFilters((prev) => ({ ...prev, search: e.target.value }))}
                                        className="flex-1 px-4 py-2 text-sm focus:outline-none"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setPage(1)}
                                        className="px-4 bg-violet-600 text-white text-sm font-medium hover:bg-violet-700"
                                    >
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-sm">
                                <thead className="bg-slate-50 text-xs text-slate-500 uppercase">
                                    <tr>
                                        <th className="px-5 py-3 text-left">Timestamp</th>
                                        <th className="px-5 py-3 text-left">Recipient</th>
                                        <th className="px-5 py-3 text-left">Subject</th>
                                        <th className="px-5 py-3 text-left">Channel</th>
                                        <th className="px-5 py-3 text-left">Status</th>
                                        <th className="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {loading ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Memuat log notifikasi...
                                            </td>
                                        </tr>
                                    ) : logs.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Belum ada log.
                                            </td>
                                        </tr>
                                    ) : (
                                        logs.map((log) => (
                                            <tr key={log.id} className="hover:bg-slate-50 transition">
                                                <td className="px-5 py-4 text-slate-700">{log.sent_at || '-'}</td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900 font-medium">{log.recipient}</p>
                                                    <p className="text-xs text-slate-400">{log.channel}</p>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900">{log.subject}</p>
                                                    <p className="text-xs text-slate-400 truncate max-w-xs">{log.message}</p>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span className="inline-flex items-center text-xs text-slate-600 space-x-1">
                                                        {log.channel === 'email' && <EnvelopeIcon className="w-3.5 h-3.5" />}
                                                        {log.channel === 'whatsapp' && <PhoneIcon className="w-3.5 h-3.5" />}
                                                        {log.channel === 'sms' && <PhoneIcon className="w-3.5 h-3.5" />}
                                                        <span>{log.channel?.toUpperCase()}</span>
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span
                                                        className={`inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${
                                                            statusBadge[log.status] || 'bg-slate-100 text-slate-600'
                                                        }`}
                                                    >
                                                        {log.status}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 text-right space-x-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => setSelectedLog(log)}
                                                        className="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50"
                                                    >
                                                        Detail
                                                    </button>
                                                    {log.status === 'failed' && canResend && (
                                                        <button
                                                            type="button"
                                                            onClick={() => handleResend(log.id)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-violet-200 text-violet-600 rounded-xl hover:bg-violet-50"
                                                        >
                                                            <PaperAirplaneIcon className="w-4 h-4 mr-1" />
                                                            Resend
                                                        </button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="flex flex-col md:flex-row md:items-center md:justify-between px-5 py-4 text-sm text-slate-600">
                            <p>
                                Halaman {pagination.current_page} dari {pagination.last_page} • Total {pagination.total} log
                            </p>
                            <div className="flex gap-2 mt-2 md:mt-0">
                                <button
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => setPage((prev) => Math.max(prev - 1, 1))}
                                    className="px-3 py-1.5 border border-slate-200 rounded-xl bg-white disabled:opacity-40"
                                >
                                    Sebelumnya
                                </button>
                                <button
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => setPage((prev) => prev + 1)}
                                    className="px-3 py-1.5 border border-slate-200 rounded-xl bg-white disabled:opacity-40"
                                >
                                    Berikutnya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {selectedLog && (
                <div className="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                            <div>
                                <p className="text-xs text-slate-500">Recipient</p>
                                <p className="text-sm font-semibold text-slate-900">{selectedLog.recipient}</p>
                                <p className="text-xs text-slate-400">{selectedLog.channel?.toUpperCase()}</p>
                            </div>
                            <button onClick={() => setSelectedLog(null)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <p className="text-xs text-slate-500">Subject</p>
                                <p className="text-base font-semibold text-slate-900">{selectedLog.subject}</p>
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Message</p>
                                <p className="text-sm text-slate-700 whitespace-pre-line">{selectedLog.message}</p>
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Metadata</p>
                                <pre className="bg-slate-900 text-green-200 text-xs rounded-xl p-4 overflow-x-auto">
                                    {JSON.stringify(selectedLog.metadata || {}, null, 2)}
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
