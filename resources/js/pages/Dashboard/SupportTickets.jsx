import { useEffect, useState } from "react";
import {
    ArrowPathIcon,
    CheckCircleIcon,
    ClipboardDocumentListIcon,
    PlusIcon,
    UserGroupIcon,
    UserPlusIcon,
    XMarkIcon,
} from "@heroicons/react/24/outline";
import DashboardLayout from "../../layouts/DashboardLayout";
import api from "../../utils/api";
import { useAuthStore } from "../../stores/authStore";

const statusStyles = {
    open: "bg-indigo-100 text-indigo-700",
    in_progress: "bg-blue-100 text-blue-700",
    resolved: "bg-emerald-100 text-emerald-700",
    closed: "bg-slate-100 text-slate-600",
};

const priorityStyles = {
    low: "bg-slate-100 text-slate-700",
    medium: "bg-amber-100 text-amber-700",
    high: "bg-orange-100 text-orange-700",
    urgent: "bg-red-100 text-red-700",
};

const defaultForm = {
    customer_id: "",
    subject: "",
    description: "",
    category: "internet",
    priority: "medium",
};
export default function SupportTickets() {
    const { user } = useAuthStore();
    const isSuperuser = (user?.role || "").toLowerCase() === "superuser";
    const canCreate = isSuperuser || user?.permissions?.includes("create_tickets");
    const canAssign = isSuperuser || user?.permissions?.includes("assign_tickets");
    const canUpdateStatus = isSuperuser || user?.permissions?.includes("resolve_tickets");
    const [tickets, setTickets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [filters, setFilters] = useState({ status: "all", priority: "all", search: "" });
    const [showCreate, setShowCreate] = useState(false);
    const [createForm, setCreateForm] = useState(defaultForm);
    const [createErrors, setCreateErrors] = useState({});
    const [customers, setCustomers] = useState([]);
    const [customersLoading, setCustomersLoading] = useState(false);
    const [teamMembers, setTeamMembers] = useState([]);
    const [selectedTicket, setSelectedTicket] = useState(null);
    const [showAssign, setShowAssign] = useState(false);
    const [assignUser, setAssignUser] = useState("");
    const [showStatusModal, setShowStatusModal] = useState(false);
    const [statusForm, setStatusForm] = useState({ status: "in_progress", note: "" });
    const [statusErrors, setStatusErrors] = useState({});

    useEffect(() => {
        fetchTickets();
    }, [page, filters]);

    useEffect(() => {
        fetchTeamMembers();
    }, []);
    const fetchTickets = async () => {
        setLoading(true);
        try {
            const params = {
                page,
                per_page: 10,
            };
            if (filters.status !== "all") params.status = filters.status;
            if (filters.priority !== "all") params.priority = filters.priority;
            if (filters.search) params.search = filters.search;

            const response = await api.get("/tickets", { params });
            setTickets(response.data.data || []);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            });
        } catch (error) {
            console.error("Failed to fetch tickets", error);
        } finally {
            setLoading(false);
        }
    };

    const fetchTeamMembers = async () => {
        try {
            const response = await api.get("/users/options", { params: { role: "teknisi" } });
            setTeamMembers(response.data.data || []);
        } catch (error) {
            console.error("Failed to load team members", error);
        }
    };

    const fetchCustomers = async () => {
        setCustomersLoading(true);
        try {
            const response = await api.get("/customers", { params: { per_page: 100 } });
            setCustomers(response.data.data || []);
        } catch (error) {
            console.error("Failed to load customers", error);
        } finally {
            setCustomersLoading(false);
        }
    };

    const openCreateModal = () => {
        setCreateErrors({});
        setCreateForm(defaultForm);
        setShowCreate(true);
        if (customers.length === 0) {
            fetchCustomers();
        }
    };

    const handleCreateTicket = async (event) => {
        event.preventDefault();
        setCreateErrors({});
        try {
            await api.post("/tickets", createForm);
            setShowCreate(false);
            fetchTickets();
        } catch (error) {
            if (error.response?.data?.errors) {
                setCreateErrors(error.response.data.errors);
            } else {
                setCreateErrors({ general: error.response?.data?.message || "Gagal membuat ticket" });
            }
        }
    };

    const openAssignModal = (ticket) => {
        setSelectedTicket(ticket);
        setAssignUser(ticket.assigned_to || "");
        setShowAssign(true);
    };

    const handleAssign = async (event) => {
        event.preventDefault();
        if (!selectedTicket) return;
        try {
            await api.post(`/tickets/${selectedTicket.id}/assign`, { assigned_to: assignUser || null });
            setShowAssign(false);
            fetchTickets();
        } catch (error) {
            alert(error.response?.data?.message || "Gagal meng-assign ticket");
        }
    };

    const openStatusModal = (ticket) => {
        setSelectedTicket(ticket);
        setStatusErrors({});
        setStatusForm({ status: ticket.status, note: "" });
        setShowStatusModal(true);
    };

    const handleStatusUpdate = async (event) => {
        event.preventDefault();
        if (!selectedTicket) return;
        setStatusErrors({});
        try {
            await api.post(`/tickets/${selectedTicket.id}/status`, statusForm);
            setShowStatusModal(false);
            fetchTickets();
        } catch (error) {
            if (error.response?.data?.errors) {
                setStatusErrors(error.response.data.errors);
            } else {
                setStatusErrors({ general: error.response?.data?.message || "Gagal mengubah status" });
            }
        }
    };

    const statusCounts = tickets.reduce(
        (acc, ticket) => {
            acc[ticket.status] = (acc[ticket.status] || 0) + 1;
            return acc;
        },
        {}
    );
    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gradient-to-br from-slate-50 via-white to-purple-50 p-6">
                <div className="max-w-7xl mx-auto space-y-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">Support Tickets</h1>
                            <p className="text-sm text-slate-500">
                                Monitoring gangguan pelanggan, assignment teknisi, dan SLA penyelesaian.
                            </p>
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={fetchTickets}
                                className="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 text-sm text-slate-700"
                            >
                                <ArrowPathIcon className="w-4 h-4 mr-2" />
                                Refresh
                            </button>
                            {canCreate && (
                                <button
                                    onClick={openCreateModal}
                                    className="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl shadow-md hover:bg-purple-700 text-sm font-semibold"
                                >
                                    <PlusIcon className="w-4 h-4 mr-2" />
                                    Ticket Baru
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {['open', 'in_progress', 'resolved', 'closed'].map((status) => (
                            <div
                                key={status}
                                className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center space-x-3"
                            >
                                <div className="p-3 rounded-2xl bg-slate-50 text-slate-600">
                                    <ClipboardDocumentListIcon className="w-5 h-5" />
                                </div>
                                <div>
                                    <p className="text-xs uppercase text-slate-400">{status.replace('_', ' ')}</p>
                                    <p className="text-xl font-bold text-slate-900">{statusCounts[status] || 0}</p>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Status</label>
                                <select
                                    value={filters.status}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, status: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Prioritas</label>
                                <select
                                    value={filters.priority}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, priority: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500"
                                >
                                    <option value="all">Semua</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div className="md:col-span-2">
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Search</label>
                                <div className="flex rounded-xl border border-slate-200 overflow-hidden">
                                    <input
                                        type="text"
                                        placeholder="Cari ticket number, customer, atau subject..."
                                        value={filters.search}
                                        onChange={(e) => setFilters((prev) => ({ ...prev, search: e.target.value }))}
                                        className="flex-1 px-4 py-2 text-sm focus:outline-none"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setPage(1)}
                                        className="px-4 bg-purple-600 text-white text-sm font-medium hover:bg-purple-700"
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
                                        <th className="px-5 py-3 text-left">Ticket</th>
                                        <th className="px-5 py-3 text-left">Customer</th>
                                        <th className="px-5 py-3 text-left">Priority</th>
                                        <th className="px-5 py-3 text-left">Status</th>
                                        <th className="px-5 py-3 text-left hidden lg:table-cell">Assigned</th>
                                        <th className="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {loading ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Memuat tickets...
                                            </td>
                                        </tr>
                                    ) : tickets.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Tidak ada ticket yang cocok.
                                            </td>
                                        </tr>
                                    ) : (
                                        tickets.map((ticket) => (
                                            <tr key={ticket.id} className="hover:bg-slate-50 transition">
                                                <td className="px-5 py-4">
                                                    <p className="font-semibold text-slate-900">{ticket.ticket_number}</p>
                                                    <p className="text-sm text-slate-600">{ticket.subject}</p>
                                                    <p className="text-xs text-slate-400">{ticket.category}</p>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900 font-medium">{ticket.customer?.name}</p>
                                                    <p className="text-xs text-slate-500">{ticket.customer?.email}</p>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span
                                                        className={`inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${
                                                            priorityStyles[ticket.priority] || "bg-slate-100 text-slate-600"
                                                        }`}
                                                    >
                                                        {ticket.priority}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span
                                                        className={`inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${
                                                            statusStyles[ticket.status] || "bg-slate-100 text-slate-600"
                                                        }`}
                                                    >
                                                        {ticket.status.replace("_", " ")}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 hidden lg:table-cell">
                                                    {ticket.assigned_to ? (
                                                        (() => {
                                                            const relation = typeof ticket.assigned_to === "object" ? ticket.assigned_to : null;
                                                            const assignedId = relation?.id ?? ticket.assigned_to;
                                                            const assignedMember =
                                                                relation ||
                                                                teamMembers.find((member) => member.id === assignedId);
                                                            return (
                                                                <div className="flex items-center space-x-2">
                                                                    <UserGroupIcon className="w-4 h-4 text-slate-400" />
                                                                    <div>
                                                                        <p className="text-sm text-slate-700">
                                                                            {assignedMember?.name || `User #${assignedId}`}
                                                                        </p>
                                                                        {assignedMember?.email && (
                                                                            <p className="text-xs text-slate-400">{assignedMember.email}</p>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            );
                                                        })()
                                                    ) : (
                                                        <span className="text-xs text-slate-400">Belum di-assign</span>
                                                    )}
                                                </td>
                                                <td className="px-5 py-4 text-right space-x-2">
                                                    {canAssign && (
                                                        <button
                                                            onClick={() => openAssignModal(ticket)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50"
                                                        >
                                                            <UserPlusIcon className="w-4 h-4 mr-1" />
                                                            Assign
                                                        </button>
                                                    )}
                                                    {canUpdateStatus && (
                                                        <button
                                                            onClick={() => openStatusModal(ticket)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-purple-200 text-purple-600 rounded-xl hover:bg-purple-50"
                                                        >
                                                            <CheckCircleIcon className="w-4 h-4 mr-1" />
                                                            Status
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
                                Halaman {pagination.current_page} dari {pagination.last_page} • Total {pagination.total} tickets
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
            {showCreate && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                            <div>
                                <h3 className="text-xl font-semibold text-slate-900">Ticket Baru</h3>
                                <p className="text-sm text-slate-500">Catat keluhan pelanggan dan assign teknisi</p>
                            </div>
                            <button onClick={() => setShowCreate(false)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <form onSubmit={handleCreateTicket} className="p-6 space-y-4">
                            {createErrors.general && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                                    {createErrors.general}
                                </div>
                            )}
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Customer</label>
                                {customersLoading ? (
                                    <p className="text-sm text-slate-500">Memuat customer...</p>
                                ) : (
                                    <select
                                        required
                                        value={createForm.customer_id}
                                        onChange={(e) => setCreateForm((prev) => ({ ...prev, customer_id: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                    >
                                        <option value="">Pilih customer...</option>
                                        {customers.map((customer) => (
                                            <option key={customer.id} value={customer.id}>
                                                {customer.name} • {customer.email}
                                            </option>
                                        ))}
                                    </select>
                                )}
                                {createErrors.customer_id && (
                                    <p className="text-xs text-red-600 mt-1">{createErrors.customer_id[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Subject</label>
                                <input
                                    type="text"
                                    required
                                    value={createForm.subject}
                                    onChange={(e) => setCreateForm((prev) => ({ ...prev, subject: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                />
                                {createErrors.subject && (
                                    <p className="text-xs text-red-600 mt-1">{createErrors.subject[0]}</p>
                                )}
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Kategori</label>
                                    <input
                                        type="text"
                                        value={createForm.category}
                                        onChange={(e) => setCreateForm((prev) => ({ ...prev, category: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                    />
                                    {createErrors.category && (
                                        <p className="text-xs text-red-600 mt-1">{createErrors.category[0]}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Prioritas</label>
                                    <select
                                        value={createForm.priority}
                                        onChange={(e) => setCreateForm((prev) => ({ ...prev, priority: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                    {createErrors.priority && (
                                        <p className="text-xs text-red-600 mt-1">{createErrors.priority[0]}</p>
                                    )}
                                </div>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Deskripsi</label>
                                <textarea
                                    rows="4"
                                    required
                                    value={createForm.description}
                                    onChange={(e) => setCreateForm((prev) => ({ ...prev, description: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                />
                                {createErrors.description && (
                                    <p className="text-xs text-red-600 mt-1">{createErrors.description[0]}</p>
                                )}
                            </div>
                            <div className="flex justify-end gap-3 pt-3">
                                <button
                                    type="button"
                                    onClick={() => setShowCreate(false)}
                                    className="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700"
                                >
                                    Simpan Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showAssign && (
                <div className="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <h3 className="text-lg font-semibold text-slate-900">Assign Teknisi</h3>
                            <button onClick={() => setShowAssign(false)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <form onSubmit={handleAssign} className="p-5 space-y-4">
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Teknisi</label>
                                <select
                                    value={assignUser}
                                    onChange={(e) => setAssignUser(e.target.value)}
                                    className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                >
                                    <option value="">Belum ditentukan</option>
                                    {teamMembers.map((member) => (
                                        <option key={member.id} value={member.id}>
                                            {member.name} • {member.email}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setShowAssign(false)}
                                    className="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700"
                                >
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showStatusModal && (
                <div className="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <div>
                                <h3 className="text-lg font-semibold text-slate-900">Ubah Status</h3>
                                <p className="text-xs text-slate-500">{selectedTicket?.ticket_number}</p>
                            </div>
                            <button onClick={() => setShowStatusModal(false)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <form onSubmit={handleStatusUpdate} className="p-5 space-y-4">
                            {statusErrors.general && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-2 text-sm">
                                    {statusErrors.general}
                                </div>
                            )}
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Status</label>
                                <select
                                    value={statusForm.status}
                                    onChange={(e) => setStatusForm((prev) => ({ ...prev, status: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                >
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                {statusErrors.status && (
                                    <p className="text-xs text-red-600 mt-1">{statusErrors.status[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Catatan</label>
                                <textarea
                                    rows="3"
                                    value={statusForm.note}
                                    onChange={(e) => setStatusForm((prev) => ({ ...prev, note: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-purple-500 focus:border-purple-500"
                                />
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setShowStatusModal(false)}
                                    className="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700"
                                >
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}

