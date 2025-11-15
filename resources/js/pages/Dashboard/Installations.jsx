import { useEffect, useState } from "react";
import {
    ArrowPathIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    MapPinIcon,
    PlusIcon,
    UserPlusIcon,
    XMarkIcon,
} from "@heroicons/react/24/outline";
import DashboardLayout from "../../layouts/DashboardLayout";
import api from "../../utils/api";
import { useAuthStore } from "../../stores/authStore";

const statusStyles = {
    scheduled: "bg-blue-100 text-blue-700",
    in_progress: "bg-amber-100 text-amber-700",
    completed: "bg-emerald-100 text-emerald-700",
    cancelled: "bg-slate-100 text-slate-600",
};

const defaultForm = {
    customer_id: "",
    package_id: "",
    scheduled_date: "",
    scheduled_time: "10:00",
    technician_id: "",
    installation_address: "",
    notes: "",
};
export default function Installations() {
    const { user } = useAuthStore();
    const isSuperuser = (user?.role || "").toLowerCase() === "superuser";
    const canCreate = isSuperuser || user?.permissions?.includes("create_installations");
    const canAssign = isSuperuser || user?.permissions?.includes("assign_installations");
    const canUpdateStatus = isSuperuser || user?.permissions?.includes("complete_installations");

    const [installations, setInstallations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [filters, setFilters] = useState({ status: "all", from: "", to: "", search: "" });
    const [showCreate, setShowCreate] = useState(false);
    const [formData, setFormData] = useState(defaultForm);
    const [formErrors, setFormErrors] = useState({});
    const [customers, setCustomers] = useState([]);
    const [packages, setPackages] = useState([]);
    const [team, setTeam] = useState([]);
    const [assignModal, setAssignModal] = useState(false);
    const [statusModal, setStatusModal] = useState(false);
    const [selectedInstallation, setSelectedInstallation] = useState(null);
    const [assignTechnician, setAssignTechnician] = useState("");
    const [statusForm, setStatusForm] = useState({ status: "in_progress", notes: "" });
    const [statusErrors, setStatusErrors] = useState({});

    useEffect(() => {
        fetchInstallations();
    }, [page, filters]);

    useEffect(() => {
        Promise.all([fetchCustomers(), fetchPackages(), fetchTeam()]);
    }, []);
    const fetchInstallations = async () => {
        setLoading(true);
        try {
            const params = { page, per_page: 10 };
            if (filters.status !== "all") params.status = filters.status;
            if (filters.from) params.from_date = filters.from;
            if (filters.to) params.to_date = filters.to;
            if (filters.search) params.search = filters.search;

            const response = await api.get("/installations", { params });
            setInstallations(response.data.data || []);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            });
        } catch (error) {
            console.error("Failed to fetch installations", error);
        } finally {
            setLoading(false);
        }
    };

    const fetchCustomers = async () => {
        try {
            const response = await api.get("/customers", { params: { per_page: 100 } });
            setCustomers(response.data.data || []);
        } catch (error) {
            console.error("Failed to fetch customers", error);
        }
    };

    const fetchPackages = async () => {
        try {
            const response = await api.get("/packages", { params: { per_page: 100 } });
            setPackages(response.data.data || []);
        } catch (error) {
            console.error("Failed to load packages", error);
        }
    };

    const fetchTeam = async () => {
        try {
            const response = await api.get("/users/options", { params: { role: "teknisi" } });
            setTeam(response.data.data || []);
        } catch (error) {
            console.error("Failed to load team", error);
        }
    };
    const openCreateModal = () => {
        setFormErrors({});
        setFormData(defaultForm);
        setShowCreate(true);
    };

    const handleCreate = async (event) => {
        event.preventDefault();
        setFormErrors({});
        try {
            await api.post("/installations", formData);
            setShowCreate(false);
            fetchInstallations();
        } catch (error) {
            if (error.response?.data?.errors) {
                setFormErrors(error.response.data.errors);
            } else {
                setFormErrors({ general: error.response?.data?.message || "Gagal membuat jadwal" });
            }
        }
    };

    const openAssignModal = (installation) => {
        setSelectedInstallation(installation);
        const relation = typeof installation.technician === "object" ? installation.technician?.id : installation.technician_id;
        setAssignTechnician(relation || "");
        setAssignModal(true);
    };

    const handleAssign = async (event) => {
        event.preventDefault();
        if (!selectedInstallation) return;
        try {
            await api.post(`/installations/${selectedInstallation.id}/assign`, { technician_id: assignTechnician });
            setAssignModal(false);
            fetchInstallations();
        } catch (error) {
            alert(error.response?.data?.message || "Gagal assign teknisi");
        }
    };

    const openStatusModal = (installation) => {
        setSelectedInstallation(installation);
        setStatusErrors({});
        setStatusForm({ status: installation.status, notes: installation.notes || "" });
        setStatusModal(true);
    };

    const handleStatusUpdate = async (event) => {
        event.preventDefault();
        if (!selectedInstallation) return;
        setStatusErrors({});
        try {
            await api.post(`/installations/${selectedInstallation.id}/status`, statusForm);
            setStatusModal(false);
            fetchInstallations();
        } catch (error) {
            if (error.response?.data?.errors) {
                setStatusErrors(error.response.data.errors);
            } else {
                setStatusErrors({ general: error.response?.data?.message || "Gagal update status" });
            }
        }
    };
    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 p-6">
                <div className="max-w-7xl mx-auto space-y-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">Installations</h1>
                            <p className="text-sm text-slate-500">Kelola jadwal instalasi lapangan dan progres teknisi.</p>
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={fetchInstallations}
                                className="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 text-sm text-slate-700"
                            >
                                <ArrowPathIcon className="w-4 h-4 mr-2" />
                                Refresh
                            </button>
                            {canCreate && (
                                <button
                                    onClick={openCreateModal}
                                    className="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-xl shadow-md hover:bg-emerald-700 text-sm font-semibold"
                                >
                                    <PlusIcon className="w-4 h-4 mr-2" />
                                    Jadwal Baru
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {['scheduled', 'in_progress', 'completed', 'cancelled'].map((status) => (
                            <div key={status} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                                <p className="text-xs uppercase text-slate-400">{status.replace('_', ' ')}</p>
                                <p className="text-2xl font-bold text-slate-900">
                                    {installations.filter((item) => item.status === status).length}
                                </p>
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
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="all">Semua</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Dari</label>
                                <input
                                    type="date"
                                    value={filters.from}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, from: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                />
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Sampai</label>
                                <input
                                    type="date"
                                    value={filters.to}
                                    onChange={(e) => {
                                        setFilters((prev) => ({ ...prev, to: e.target.value }));
                                        setPage(1);
                                    }}
                                    className="w-full border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                />
                            </div>
                            <div>
                                <label className="text-xs text-slate-500 uppercase mb-2 block">Search</label>
                                <div className="flex rounded-xl border border-slate-200 overflow-hidden">
                                    <input
                                        type="text"
                                        placeholder="Cari nomor instalasi / pelanggan"
                                        value={filters.search}
                                        onChange={(e) => setFilters((prev) => ({ ...prev, search: e.target.value }))}
                                        className="flex-1 px-4 py-2 text-sm focus:outline-none"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setPage(1)}
                                        className="px-4 bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700"
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
                                        <th className="px-5 py-3 text-left">Instalasi</th>
                                        <th className="px-5 py-3 text-left">Customer</th>
                                        <th className="px-5 py-3 text-left">Jadwal</th>
                                        <th className="px-5 py-3 text-left">Teknisi</th>
                                        <th className="px-5 py-3 text-left">Status</th>
                                        <th className="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {loading ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Memuat jadwal instalasi...
                                            </td>
                                        </tr>
                                    ) : installations.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="text-center py-10 text-slate-500">
                                                Tidak ada jadwal ditemukan.
                                            </td>
                                        </tr>
                                    ) : (
                                        installations.map((installation) => (
                                            <tr key={installation.id} className="hover:bg-slate-50 transition">
                                                <td className="px-5 py-4">
                                                    <p className="font-semibold text-slate-900">{installation.installation_number}</p>
                                                    <p className="text-xs text-slate-500">{installation.package?.name}</p>
                                                </td>
                                                <td className="px-5 py-4">
                                                    <p className="text-slate-900 font-medium">{installation.customer?.name}</p>
                                                    <p className="text-xs text-slate-500">{installation.customer?.phone}</p>
                                                    {installation.installation_address && (
                                                        <div className="flex items-center text-xs text-slate-400 space-x-1 mt-1">
                                                            <MapPinIcon className="w-3 h-3" />
                                                            <span>{installation.installation_address}</span>
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="px-5 py-4">
                                                    <div className="flex items-center space-x-2">
                                                        <CalendarDaysIcon className="w-4 h-4 text-slate-400" />
                                                        <div>
                                                            <p className="text-sm text-slate-700">{installation.scheduled_date}</p>
                                                            <p className="text-xs text-slate-400">{installation.scheduled_time}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-5 py-4">
                                                    {installation.technician ? (
                                                        <p className="text-sm text-slate-700">{installation.technician.name}</p>
                                                    ) : (
                                                        <span className="text-xs text-slate-400">Belum di-assign</span>
                                                    )}
                                                </td>
                                                <td className="px-5 py-4">
                                                    <span
                                                        className={`inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${
                                                            statusStyles[installation.status] || "bg-slate-100 text-slate-600"
                                                        }`}
                                                    >
                                                        {installation.status.replace("_", " ")}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 text-right space-x-2">
                                                    {canAssign && (
                                                        <button
                                                            onClick={() => openAssignModal(installation)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50"
                                                        >
                                                            <UserPlusIcon className="w-4 h-4 mr-1" />
                                                            Assign
                                                        </button>
                                                    )}
                                                    {canUpdateStatus && (
                                                        <button
                                                            onClick={() => openStatusModal(installation)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-emerald-200 text-emerald-600 rounded-xl hover:bg-emerald-50"
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
                                Halaman {pagination.current_page} dari {pagination.last_page} • Total {pagination.total} instalasi
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
                                <h3 className="text-xl font-semibold text-slate-900">Jadwal Instalasi</h3>
                                <p className="text-sm text-slate-500">Set jadwal kunjungan teknisi ke lokasi pelanggan.</p>
                            </div>
                            <button onClick={() => setShowCreate(false)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <form onSubmit={handleCreate} className="p-6 space-y-4">
                            {formErrors.general && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                                    {formErrors.general}
                                </div>
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Customer</label>
                                    <select
                                        required
                                        value={formData.customer_id}
                                        onChange={(e) => setFormData((prev) => ({ ...prev, customer_id: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="">Pilih customer...</option>
                                        {customers.map((customer) => (
                                            <option key={customer.id} value={customer.id}>
                                                {customer.name}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.customer_id && (
                                        <p className="text-xs text-red-600 mt-1">{formErrors.customer_id[0]}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Package</label>
                                    <select
                                        required
                                        value={formData.package_id}
                                        onChange={(e) => setFormData((prev) => ({ ...prev, package_id: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="">Pilih paket...</option>
                                        {packages.map((item) => (
                                            <option key={item.id} value={item.id}>
                                                {item.name}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.package_id && (
                                        <p className="text-xs text-red-600 mt-1">{formErrors.package_id[0]}</p>
                                    )}
                                </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Tanggal</label>
                                    <input
                                        type="date"
                                        required
                                        value={formData.scheduled_date}
                                        onChange={(e) => setFormData((prev) => ({ ...prev, scheduled_date: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                    />
                                    {formErrors.scheduled_date && (
                                        <p className="text-xs text-red-600 mt-1">{formErrors.scheduled_date[0]}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-slate-700 block mb-1">Jam</label>
                                    <input
                                        type="time"
                                        required
                                        value={formData.scheduled_time}
                                        onChange={(e) => setFormData((prev) => ({ ...prev, scheduled_time: e.target.value }))}
                                        className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                    />
                                    {formErrors.scheduled_time && (
                                        <p className="text-xs text-red-600 mt-1">{formErrors.scheduled_time[0]}</p>
                                    )}
                                </div>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Alamat Instalasi</label>
                                <textarea
                                    rows="3"
                                    required
                                    value={formData.installation_address}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, installation_address: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                />
                                {formErrors.installation_address && (
                                    <p className="text-xs text-red-600 mt-1">{formErrors.installation_address[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Teknisi</label>
                                <select
                                    value={formData.technician_id}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, technician_id: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="">Belum ditentukan</option>
                                    {team.map((technician) => (
                                        <option key={technician.id} value={technician.id}>
                                            {technician.name}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.technician_id && (
                                    <p className="text-xs text-red-600 mt-1">{formErrors.technician_id[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Catatan</label>
                                <textarea
                                    rows="2"
                                    value={formData.notes}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, notes: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                />
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
                                    className="px-5 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700"
                                >
                                    Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {assignModal && (
                <div className="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <h3 className="text-lg font-semibold text-slate-900">Assign Teknisi</h3>
                            <button onClick={() => setAssignModal(false)} className="p-2 rounded-full hover:bg-slate-100">
                                <XMarkIcon className="w-5 h-5 text-slate-500" />
                            </button>
                        </div>
                        <form onSubmit={handleAssign} className="p-5 space-y-4">
                            <select
                                value={assignTechnician}
                                onChange={(e) => setAssignTechnician(e.target.value)}
                                className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="">Pilih teknisi...</option>
                                {team.map((technician) => (
                                    <option key={technician.id} value={technician.id}>
                                        {technician.name}
                                    </option>
                                ))}
                            </select>
                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setAssignModal(false)}
                                    className="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700"
                                >
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {statusModal && (
                <div className="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <div>
                                <h3 className="text-lg font-semibold text-slate-900">Update Status</h3>
                                <p className="text-xs text-slate-500">{selectedInstallation?.installation_number}</p>
                            </div>
                            <button onClick={() => setStatusModal(false)} className="p-2 rounded-full hover:bg-slate-100">
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
                                    className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="scheduled">Scheduled</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                {statusErrors.status && (
                                    <p className="text-xs text-red-600 mt-1">{statusErrors.status[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 block mb-1">Catatan</label>
                                <textarea
                                    rows="3"
                                    value={statusForm.notes}
                                    onChange={(e) => setStatusForm((prev) => ({ ...prev, notes: e.target.value }))}
                                    className="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                                />
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setStatusModal(false)}
                                    className="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700"
                                >
                                    Simpan Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}

