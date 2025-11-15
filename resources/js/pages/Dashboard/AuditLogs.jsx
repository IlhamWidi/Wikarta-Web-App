import { useCallback, useEffect, useState } from "react";
import {
    ArrowPathIcon,
    EyeIcon,
    MagnifyingGlassIcon,
    ShieldCheckIcon,
    XMarkIcon,
} from "@heroicons/react/24/outline";
import { format } from "date-fns";
import DashboardLayout from "../../layouts/DashboardLayout";
import api from "../../utils/api";

const eventOptions = [
    { value: "all", label: "Semua Event" },
    { value: "payment.created", label: "Payment Created" },
    { value: "payment.verified", label: "Payment Verified" },
    { value: "invoice.voided", label: "Invoice Voided" },
    { value: "invoice.refunded", label: "Invoice Refunded" },
];

const formatDate = (date) => {
    if (!date) return "-";
    try {
        return format(new Date(date), "dd MMM yyyy HH:mm:ss");
    } catch (error) {
        return date;
    }
};

const formatEntity = (value) => {
    if (!value) return "-";
    const segments = value.split("\\");
    return segments[segments.length - 1];
};

const JsonViewer = ({ data }) => {
    if (!data || Object.keys(data || {}).length === 0) {
        return <p className="text-sm text-gray-500">Tidak ada data</p>;
    }

    return (
        <pre className="bg-gray-900 text-green-200 text-xs rounded-xl p-4 overflow-x-auto whitespace-pre-wrap break-words">
            {JSON.stringify(data, null, 2)}
        </pre>
    );
};

export default function AuditLogs() {
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [filters, setFilters] = useState({ search: "", event: "all", from: "", to: "" });
    const [selectedLog, setSelectedLog] = useState(null);
    const [showDetail, setShowDetail] = useState(false);

    const fetchLogs = useCallback(async () => {
        setLoading(true);
        try {
            const params = {
                page,
                per_page: 15,
            };

            if (filters.search) params.search = filters.search;
            if (filters.event !== "all") params.event = filters.event;
            if (filters.from) params.from_date = filters.from;
            if (filters.to) params.to_date = filters.to;

            const response = await api.get("/audit-logs", { params });
            setLogs(response.data.data || []);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            });
        } catch (error) {
            console.error("Failed to fetch audit logs", error);
        } finally {
            setLoading(false);
        }
    }, [filters.event, filters.from, filters.search, filters.to, page]);

    useEffect(() => {
        fetchLogs();
    }, [fetchLogs]);

    const resetFilters = () => {
        setFilters({ search: "", event: "all", from: "", to: "" });
        setPage(1);
    };

    const openDetail = (log) => {
        setSelectedLog(log);
        setShowDetail(true);
    };

    const closeDetail = () => {
        setSelectedLog(null);
        setShowDetail(false);
    };

    return (
        <DashboardLayout>
            <div className="min-h-screen bg-slate-50 p-6">
                <div className="max-w-7xl mx-auto space-y-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">Audit Logs</h1>
                            <p className="text-sm text-slate-500">
                                Jejak aktivitas sensitif untuk memenuhi kontrol RBAC & compliance
                            </p>
                        </div>
                        <button
                            onClick={fetchLogs}
                            className="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 text-sm text-slate-700"
                        >
                            <ArrowPathIcon className="w-4 h-4 mr-2" />
                            Refresh
                        </button>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="bg-white border border-slate-100 rounded-2xl p-4 flex items-center space-x-4">
                            <div className="p-3 bg-violet-50 rounded-2xl text-violet-600">
                                <ShieldCheckIcon className="w-6 h-6" />
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Total Logs (page)</p>
                                <p className="text-xl font-semibold text-slate-900">{logs.length}</p>
                            </div>
                        </div>
                        <div className="bg-white border border-slate-100 rounded-2xl p-4">
                            <p className="text-xs text-slate-500 mb-1">Filter Event</p>
                            <select
                                value={filters.event}
                                onChange={(e) => {
                                    setFilters((prev) => ({ ...prev, event: e.target.value }));
                                    setPage(1);
                                }}
                                className="w-full border-slate-200 rounded-xl text-sm focus:ring-violet-500 focus:border-violet-500"
                            >
                                {eventOptions.map((option) => (
                                    <option key={option.value} value={option.value}>
                                        {option.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="bg-white border border-slate-100 rounded-2xl p-4">
                            <p className="text-xs text-slate-500 mb-1">Range Waktu</p>
                            <div className="flex gap-2 text-sm">
                                <input
                                    type="date"
                                    value={filters.from}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, from: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="flex-1 border-slate-200 rounded-xl focus:ring-violet-500 focus:border-violet-500"
                                />
                                <input
                                    type="date"
                                    value={filters.to}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, to: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="flex-1 border-slate-200 rounded-xl focus:ring-violet-500 focus:border-violet-500"
                                />
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div className="flex-1 flex rounded-xl border border-slate-200 overflow-hidden">
                                <input
                                    type="text"
                                    placeholder="Cari trace ID, event, atau deskripsi..."
                                    value={filters.search}
                                    onChange={(e) => setFilters((prev) => ({ ...prev, search: e.target.value }))}
                                    className="flex-1 px-4 py-2 text-sm focus:outline-none"
                                />
                                <button
                                    onClick={() => setPage(1)}
                                    className="px-4 bg-violet-600 text-white text-sm font-medium hover:bg-violet-700"
                                >
                                    <MagnifyingGlassIcon className="w-4 h-4" />
                                </button>
                            </div>
                            <button
                                onClick={resetFilters}
                                className="text-sm text-slate-500 hover:text-slate-700"
                            >
                                Reset filter
                            </button>
                        </div>

                        <div className="overflow-x-auto rounded-2xl border border-slate-100">
                            <table className="min-w-full text-sm">
                                <thead className="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                                    <tr>
                                        <th className="px-5 py-3 text-left">Trace ID</th>
                                        <th className="px-5 py-3 text-left">Event</th>
                                        <th className="px-5 py-3 text-left">User</th>
                                        <th className="px-5 py-3 text-left">Entity</th>
                                        <th className="px-5 py-3 text-left">Timestamp</th>
                                        <th className="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {loading ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Memuat audit logs...
                                            </td>
                                        </tr>
                                    ) : logs.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Tidak ada log yang cocok dengan filter.
                                            </td>
                                        </tr>
                                    ) : (
                                        logs.map((log) => (
                                            <tr key={log.id} className="hover:bg-slate-50 transition">
                                                <td className="px-5 py-4 font-mono text-xs text-slate-600">
                                                    {log.trace_id}
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span className="px-2 py-1 text-xs rounded-full bg-violet-50 text-violet-700 font-semibold">
                                                        {log.event}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900 font-medium">{log.user?.name || "System"}</p>
                                                    {log.user?.email && (
                                                        <p className="text-xs text-slate-500">{log.user.email}</p>
                                                    )}
                                                </td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900">{formatEntity(log.auditable_type)}</p>
                                                    {log.auditable_id && (
                                                        <p className="text-xs text-slate-500">ID #{log.auditable_id}</p>
                                                    )}
                                                </td>
                                                <td className="px-5 py-4 text-slate-700">
                                                    {formatDate(log.created_at)}
                                                </td>
                                                <td className="px-5 py-4 text-right">
                                                    <button
                                                        type="button"
                                                        onClick={() => openDetail(log)}
                                                        className="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50"
                                                    >
                                                        <EyeIcon className="w-4 h-4 mr-1" />
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="flex flex-col md:flex-row md:items-center md:justify-between text-sm text-slate-600">
                            <p>
                                Halaman {pagination.current_page} dari {pagination.last_page} • Total {pagination.total} log
                            </p>
                            <div className="flex gap-2 mt-3 md:mt-0">
                                <button
                                    type="button"
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => setPage((prev) => Math.max(prev - 1, 1))}
                                    className="px-3 py-1.5 border border-slate-200 rounded-xl bg-white disabled:opacity-40"
                                >
                                    Previous
                                </button>
                                <button
                                    type="button"
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => setPage((prev) => prev + 1)}
                                    className="px-3 py-1.5 border border-slate-200 rounded-xl bg-white disabled:opacity-40"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {showDetail && selectedLog && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                        <div className="flex items-start justify-between px-6 py-5 border-b border-slate-100">
                            <div>
                                <p className="text-xs text-slate-500">Trace ID</p>
                                <p className="font-mono text-sm text-slate-700">{selectedLog.trace_id}</p>
                                <p className="text-lg font-semibold text-slate-900 mt-2">{selectedLog.event}</p>
                                <p className="text-sm text-slate-500">{formatDate(selectedLog.created_at)}</p>
                            </div>
                            <button
                                type="button"
                                onClick={closeDetail}
                                className="p-2 rounded-full hover:bg-slate-100"
                            >
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>

                        <div className="p-6 space-y-5">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="bg-slate-50 rounded-2xl p-4">
                                    <p className="text-xs text-slate-500 uppercase mb-1">User</p>
                                    <p className="text-sm font-semibold text-slate-900">
                                        {selectedLog.user?.name || "System"}
                                    </p>
                                    {selectedLog.user?.email && (
                                        <p className="text-xs text-slate-500">{selectedLog.user.email}</p>
                                    )}
                                </div>
                                <div className="bg-slate-50 rounded-2xl p-4">
                                    <p className="text-xs text-slate-500 uppercase mb-1">Entity</p>
                                    <p className="text-sm font-semibold text-slate-900">
                                        {formatEntity(selectedLog.auditable_type)}
                                    </p>
                                    {selectedLog.auditable_id && (
                                        <p className="text-xs text-slate-500">ID #{selectedLog.auditable_id}</p>
                                    )}
                                </div>
                            </div>

                            {selectedLog.description && (
                                <div className="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-4 text-sm">
                                    <p className="font-semibold mb-1">Deskripsi</p>
                                    <p>{selectedLog.description}</p>
                                </div>
                            )}

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm font-semibold text-slate-700 mb-2">Old Values</p>
                                    <JsonViewer data={selectedLog.old_values} />
                                </div>
                                <div>
                                    <p className="text-sm font-semibold text-slate-700 mb-2">New Values</p>
                                    <JsonViewer data={selectedLog.new_values} />
                                </div>
                            </div>

                            {selectedLog.metadata && (
                                <div>
                                    <p className="text-sm font-semibold text-slate-700 mb-2">Metadata</p>
                                    <JsonViewer data={selectedLog.metadata} />
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
