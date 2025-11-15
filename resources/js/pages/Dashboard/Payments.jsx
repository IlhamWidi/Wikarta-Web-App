import { useEffect, useMemo, useState } from "react";
import {
    ArrowPathIcon,
    CheckCircleIcon,
    ClipboardDocumentIcon,
    CreditCardIcon,
    EyeIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    XMarkIcon,
} from "@heroicons/react/24/outline";
import { format } from "date-fns";
import DashboardLayout from "../../layouts/DashboardLayout";
import api from "../../utils/api";
import toast from 'react-hot-toast';

const statusStyles = {
    pending: "bg-yellow-100 text-yellow-800",
    settlement: "bg-green-100 text-green-800",
    capture: "bg-green-100 text-green-800",
    deny: "bg-red-100 text-red-800",
    cancel: "bg-red-100 text-red-800",
    expire: "bg-gray-100 text-gray-600",
    refund: "bg-purple-100 text-purple-800",
    partial_refund: "bg-purple-100 text-purple-800",
    challenge: "bg-orange-100 text-orange-800",
};

const methodLabels = {
    virtual_account: "Virtual Account",
    qris: "QRIS",
    credit_card: "Credit Card",
    e_wallet: "E-Wallet",
    bank_transfer: "Bank Transfer",
    cash: "Cash",
    other: "Other",
};

const bankOptions = [
    { value: "bca", label: "BCA" },
    { value: "bni", label: "BNI" },
    { value: "bri", label: "BRI" },
    { value: "permata", label: "Permata" },
    { value: "mandiri", label: "Mandiri" },
];

const formatCurrency = (amount = 0) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(Number(amount || 0));

const formatDate = (date, withTime = true) => {
    if (!date) return "-";
    try {
        return format(new Date(date), withTime ? "dd MMM yyyy HH:mm" : "dd MMM yyyy");
    } catch (error) {
        return date;
    }
};

const initialCreateForm = {
    invoice_id: "",
    payment_method: "virtual_account",
    payment_type: "",
    amount: "",
    bank: "bca",
    notes: "",
};
export default function Payments() {
    const [payments, setPayments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [filters, setFilters] = useState({ status: "all", method: "all", search: "" });
    const [searchInput, setSearchInput] = useState("");
    const [selectedPayment, setSelectedPayment] = useState(null);
    const [showDetail, setShowDetail] = useState(false);
    const [showCreate, setShowCreate] = useState(false);
    const [showVerify, setShowVerify] = useState(false);
    const [createForm, setCreateForm] = useState(initialCreateForm);
    const [createErrors, setCreateErrors] = useState({});
    const [creating, setCreating] = useState(false);
    const [paymentInfo, setPaymentInfo] = useState(null);
    const [invoiceOptions, setInvoiceOptions] = useState([]);
    const [invoiceLoading, setInvoiceLoading] = useState(false);
    const [invoiceError, setInvoiceError] = useState("");
    const [verifyForm, setVerifyForm] = useState({ status: "settlement", notes: "" });
    const [verifyErrors, setVerifyErrors] = useState({});
    const [verifying, setVerifying] = useState(false);

    useEffect(() => {
        fetchPayments(page, filters);
    }, [page, filters]);

    const fetchPayments = async (pageNumber = 1, currentFilters = filters) => {
        setLoading(true);
        try {
            const params = {
                page: pageNumber,
                per_page: 10,
            };

            if (currentFilters.status !== "all") {
                params.status = currentFilters.status;
            }
            if (currentFilters.method !== "all") {
                params.payment_method = currentFilters.method;
            }
            if (currentFilters.search) {
                params.search = currentFilters.search;
            }

            const response = await api.get("/payments", { params });
            setPayments(response.data.data || []);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            });
        } catch (error) {
            console.error("Failed to fetch payments", error);
        } finally {
            setLoading(false);
        }
    };

    const refreshPayments = () => {
        fetchPayments(page, filters);
    };

    const applySearch = () => {
        setFilters((prev) => ({ ...prev, search: searchInput }));
        setPage(1);
    };

    const handleFilterChange = (field, value) => {
        setFilters((prev) => ({ ...prev, [field]: value }));
        setPage(1);
    };

    const resetFilters = () => {
        setSearchInput("");
        setFilters({ status: "all", method: "all", search: "" });
        setPage(1);
    };

    const loadInvoiceOptions = async () => {
        setInvoiceLoading(true);
        setInvoiceError("");
        try {
            const response = await api.get("/invoices", {
                params: { status: "issued", per_page: 50 },
            });
            setInvoiceOptions(response.data.data || []);
        } catch (error) {
            console.error("Failed to load invoices", error);
            setInvoiceError("Gagal memuat data invoice. Coba lagi.");
        } finally {
            setInvoiceLoading(false);
        }
    };

    const handleOpenCreate = () => {
        setCreateErrors({});
        setPaymentInfo(null);
        setCreateForm(initialCreateForm);
        setShowCreate(true);
        loadInvoiceOptions();
    };

    const handleInvoiceSelect = (value) => {
        const invoice = invoiceOptions.find((inv) => String(inv.id) === value);
        setCreateForm((prev) => ({
            ...prev,
            invoice_id: value,
            amount: invoice ? Number(invoice.total) : "",
        }));
    };

    const handleCreatePayment = async (event) => {
        event.preventDefault();
        setCreating(true);
        setCreateErrors({});
        setPaymentInfo(null);

        const payload = {
            invoice_id: createForm.invoice_id,
            payment_method: createForm.payment_method,
            payment_type: createForm.payment_type || null,
            amount: Number(createForm.amount),
            notes: createForm.notes || null,
        };

        if (createForm.payment_method === "virtual_account") {
            payload.bank = createForm.bank;
        }

        try {
            const response = await api.post("/payments", payload);
            setPaymentInfo(response.data.payment_info || null);
            setCreateForm(initialCreateForm);
            fetchPayments(page, filters);
        } catch (error) {
            if (error.response?.data?.errors) {
                setCreateErrors(error.response.data.errors);
            } else {
                setCreateErrors({ general: error.response?.data?.message || "Gagal membuat pembayaran" });
            }
        } finally {
            setCreating(false);
        }
    };

    const openDetail = (payment) => {
        setSelectedPayment(payment);
        setShowDetail(true);
    };

    const openVerify = (payment) => {
        setSelectedPayment(payment);
        setVerifyForm({ status: "settlement", notes: "" });
        setVerifyErrors({});
        setShowVerify(true);
    };
    const handleVerify = async (event) => {
        event.preventDefault();
        if (!selectedPayment) return;

        setVerifying(true);
        setVerifyErrors({});

        try {
            await api.post(`/payments/${selectedPayment.id}/verify`, verifyForm);
            setShowVerify(false);
            setSelectedPayment(null);
            fetchPayments(page, filters);
        } catch (error) {
            if (error.response?.data?.errors) {
                setVerifyErrors(error.response.data.errors);
            } else {
                setVerifyErrors({ general: error.response?.data?.message || "Gagal memverifikasi pembayaran" });
            }
        } finally {
            setVerifying(false);
        }
    };

    const summary = useMemo(() => {
        const pending = payments.filter((payment) => payment.status === "pending").length;
        const settledAmount = payments
            .filter((payment) => ["settlement", "capture"].includes(payment.status))
            .reduce((total, payment) => total + Number(payment.amount || 0), 0);
        const totalAmount = payments.reduce((total, payment) => total + Number(payment.amount || 0), 0);

        return { pending, settledAmount, totalAmount };
    }, [payments]);

    const copyToClipboard = async (value) => {
        try {
            await navigator.clipboard.writeText(value);
            toast.success("Berhasil disalin ke clipboard");
        } catch (error) {
            toast.error("Gagal menyalin ke clipboard");
        }
    };

    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6">
                <div className="max-w-7xl mx-auto space-y-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900">Payments</h1>
                            <p className="text-gray-500">Monitoring transaksi Midtrans dan verifikasi manual</p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <button
                                onClick={refreshPayments}
                                className="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 text-sm font-medium text-gray-700 transition"
                            >
                                <ArrowPathIcon className="w-4 h-4 mr-2" />
                                Refresh
                            </button>
                            <button
                                onClick={handleOpenCreate}
                                className="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 text-sm font-semibold transition"
                            >
                                <PlusIcon className="w-4 h-4 mr-2" />
                                Pembayaran Baru
                            </button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center space-x-4">
                            <div className="p-3 rounded-full bg-blue-50 text-blue-600">
                                <CreditCardIcon className="w-6 h-6" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Total Amount (page)</p>
                                <p className="text-xl font-semibold text-gray-900">{formatCurrency(summary.totalAmount)}</p>
                            </div>
                        </div>
                        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center space-x-4">
                            <div className="p-3 rounded-full bg-yellow-50 text-yellow-600">
                                <ArrowPathIcon className="w-6 h-6" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Pending Payments</p>
                                <p className="text-xl font-semibold text-gray-900">{summary.pending}</p>
                            </div>
                        </div>
                        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center space-x-4">
                            <div className="p-3 rounded-full bg-green-50 text-green-600">
                                <CheckCircleIcon className="w-6 h-6" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Settled Amount (page)</p>
                                <p className="text-xl font-semibold text-gray-900">{formatCurrency(summary.settledAmount)}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="text-sm font-medium text-gray-600 mb-2 block">Status</label>
                                <select
                                    value={filters.status}
                                    onChange={(e) => handleFilterChange("status", e.target.value)}
                                    className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="settlement">Settlement</option>
                                    <option value="capture">Capture</option>
                                    <option value="deny">Deny</option>
                                    <option value="cancel">Cancel</option>
                                    <option value="expire">Expired</option>
                                    <option value="refund">Refund</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600 mb-2 block">Metode Pembayaran</label>
                                <select
                                    value={filters.method}
                                    onChange={(e) => handleFilterChange("method", e.target.value)}
                                    className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="all">Semua Metode</option>
                                    {Object.entries(methodLabels).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600 mb-2 block">Search</label>
                                <div className="flex rounded-lg shadow-sm">
                                    <input
                                        type="text"
                                        placeholder="Payment code, invoice, VA..."
                                        value={searchInput}
                                        onChange={(e) => setSearchInput(e.target.value)}
                                        className="flex-1 border border-gray-200 rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500 px-3"
                                    />
                                    <button
                                        type="button"
                                        onClick={applySearch}
                                        className="px-4 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700"
                                    >
                                        <MagnifyingGlassIcon className="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <button
                                type="button"
                                onClick={resetFilters}
                                className="text-sm text-gray-500 hover:text-gray-700 underline"
                            >
                                Reset filter
                            </button>
                        </div>
                    </div>
                    <div className="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Payment
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Invoice
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Method
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-100">
                                    {loading ? (
                                        <tr>
                                            <td colSpan={7} className="py-12 text-center text-gray-500">
                                                Memuat data payments...
                                            </td>
                                        </tr>
                                    ) : payments.length === 0 ? (
                                        <tr>
                                            <td colSpan={7} className="py-12 text-center text-gray-500">
                                                Tidak ada data pembayaran.
                                            </td>
                                        </tr>
                                    ) : (
                                        payments.map((payment) => (
                                            <tr key={payment.id} className="hover:bg-gray-50 transition">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-semibold text-gray-900">
                                                        {payment.payment_code}
                                                    </div>
                                                    <div className="text-xs text-gray-500">
                                                        Order ID: {payment.order_id}
                                                    </div>
                                                    <div className="text-xs text-gray-400">
                                                        {formatDate(payment.created_at)}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-900">
                                                        {payment.invoice?.invoice_number || "-"}
                                                    </div>
                                                    <div className="text-xs text-gray-500">
                                                        Jatuh tempo: {formatDate(payment.invoice?.due_date, false)}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-900">
                                                        {payment.customer?.name || "-"}
                                                    </div>
                                                    <div className="text-xs text-gray-500">{payment.customer?.email}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    {formatCurrency(payment.amount)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="text-sm text-gray-700">
                                                        {methodLabels[payment.payment_method] || payment.payment_method}
                                                    </span>
                                                    {payment.payment_type && (
                                                        <p className="text-xs text-gray-500">{payment.payment_type}</p>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        className={`inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${
                                                            statusStyles[payment.status] || "bg-gray-100 text-gray-600"
                                                        }`}
                                                    >
                                                        {payment.status}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => openDetail(payment)}
                                                        className="inline-flex items-center px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 text-sm"
                                                    >
                                                        <EyeIcon className="w-4 h-4 mr-1" />
                                                        Detail
                                                    </button>
                                                    {payment.status === "pending" && (
                                                        <button
                                                            type="button"
                                                            onClick={() => openVerify(payment)}
                                                            className="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-indigo-600 rounded-lg hover:bg-indigo-50 text-sm"
                                                        >
                                                            <CheckCircleIcon className="w-4 h-4 mr-1" />
                                                            Verify
                                                        </button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="bg-gray-50 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between text-sm text-gray-600 gap-2">
                            <div>
                                Menampilkan halaman {pagination.current_page} dari {pagination.last_page} � Total {pagination.total} payments
                            </div>
                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => setPage((prev) => Math.max(prev - 1, 1))}
                                    className="px-3 py-1.5 border border-gray-200 rounded-lg bg-white disabled:opacity-40"
                                >
                                    Previous
                                </button>
                                <button
                                    type="button"
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => setPage((prev) => prev + 1)}
                                    className="px-3 py-1.5 border border-gray-200 rounded-lg bg-white disabled:opacity-40"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {showDetail && selectedPayment && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 className="text-xl font-semibold text-gray-900">Payment Detail</h3>
                                <p className="text-sm text-gray-500">{selectedPayment.payment_code}</p>
                            </div>
                            <button
                                type="button"
                                onClick={() => {
                                    setShowDetail(false);
                                    setSelectedPayment(null);
                                }}
                                className="p-2 rounded-full hover:bg-gray-100"
                            >
                                <XMarkIcon className="w-5 h-5 text-gray-500" />
                            </button>
                        </div>
                        <div className="p-6 space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div className="bg-gray-50 rounded-xl p-4 space-y-2">
                                    <h4 className="text-sm font-semibold text-gray-600">Informasi Pembayaran</h4>
                                    <div className="text-sm text-gray-700">
                                        <p>
                                            Status:
                                            <span className="font-semibold text-gray-900"> {selectedPayment.status}</span>
                                        </p>
                                        <p>
                                            Metode: {methodLabels[selectedPayment.payment_method] || selectedPayment.payment_method}
                                        </p>
                                        {selectedPayment.payment_type && <p>Type: {selectedPayment.payment_type}</p>}
                                        <p>Amount: {formatCurrency(selectedPayment.amount)}</p>
                                        <p>Dibuat: {formatDate(selectedPayment.created_at)}</p>
                                        {selectedPayment.settlement_time && (
                                            <p>Settlement: {formatDate(selectedPayment.settlement_time)}</p>
                                        )}
                                    </div>
                                </div>
                                <div className="bg-gray-50 rounded-xl p-4 space-y-2">
                                    <h4 className="text-sm font-semibold text-gray-600">Customer & Invoice</h4>
                                    <div className="text-sm text-gray-700">
                                        <p>Customer: {selectedPayment.customer?.name}</p>
                                        <p>Email: {selectedPayment.customer?.email}</p>
                                        <p>Invoice: {selectedPayment.invoice?.invoice_number}</p>
                                        <p>Order ID: {selectedPayment.order_id}</p>
                                        {selectedPayment.invoice?.due_date && (
                                            <p>Jatuh tempo: {formatDate(selectedPayment.invoice?.due_date, false)}</p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {selectedPayment.va_number && (
                                    <div className="border border-dashed border-indigo-200 rounded-xl p-4">
                                        <p className="text-sm font-semibold text-gray-700 mb-2">Virtual Account</p>
                                        <div className="flex items-center justify-between bg-indigo-50 rounded-lg px-4 py-3">
                                            <div>
                                                <p className="text-xs text-indigo-500 uppercase">Nomor VA</p>
                                                <p className="text-lg font-bold text-indigo-700">{selectedPayment.va_number}</p>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => copyToClipboard(selectedPayment.va_number)}
                                                className="p-2 rounded-lg bg-white text-indigo-600 border border-indigo-100 hover:bg-indigo-100"
                                            >
                                                <ClipboardDocumentIcon className="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                )}

                                {selectedPayment.payment_url && (
                                    <div className="border border-dashed border-purple-200 rounded-xl p-4">
                                        <p className="text-sm font-semibold text-gray-700 mb-2">Snap Payment Page</p>
                                        <a
                                            href={selectedPayment.payment_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700"
                                        >
                                            Buka Halaman Pembayaran
                                        </a>
                                    </div>
                                )}

                                {selectedPayment.qr_code_url && (
                                    <div className="border border-dashed border-green-200 rounded-xl p-4 md:col-span-2">
                                        <p className="text-sm font-semibold text-gray-700 mb-2">QRIS</p>
                                        <a
                                            href={selectedPayment.qr_code_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700"
                                        >
                                            Tampilkan QR
                                        </a>
                                    </div>
                                )}
                            </div>

                            {selectedPayment.notes && (
                                <div className="bg-yellow-50 rounded-xl p-4 text-sm text-yellow-800">
                                    <p className="font-semibold mb-1">Catatan</p>
                                    <p>{selectedPayment.notes}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
            {showCreate && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 className="text-xl font-semibold text-gray-900">Buat Pembayaran Baru</h3>
                                <p className="text-sm text-gray-500">
                                    Generate transaksi Midtrans untuk invoice yang belum dibayar
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={() => setShowCreate(false)}
                                className="p-2 rounded-full hover:bg-gray-100"
                            >
                                <XMarkIcon className="w-5 h-5 text-gray-500" />
                            </button>
                        </div>

                        <form onSubmit={handleCreatePayment} className="p-6 space-y-5">
                            {createErrors.general && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                                    {createErrors.general}
                                </div>
                            )}

                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">Pilih Invoice</label>
                                {invoiceLoading ? (
                                    <p className="text-sm text-gray-500">Memuat invoice...</p>
                                ) : invoiceError ? (
                                    <p className="text-sm text-red-600">{invoiceError}</p>
                                ) : (
                                    <select
                                        required
                                        value={createForm.invoice_id}
                                        onChange={(e) => handleInvoiceSelect(e.target.value)}
                                        className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">Pilih invoice...</option>
                                        {invoiceOptions.map((invoice) => (
                                            <option key={invoice.id} value={invoice.id}>
                                                {invoice.invoice_number} � {invoice.customer?.name} ({formatCurrency(invoice.total)})
                                            </option>
                                        ))}
                                    </select>
                                )}
                                {createErrors.invoice_id && (
                                    <p className="text-xs text-red-600 mt-1">{createErrors.invoice_id[0]}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-700 block mb-1">Nominal Pembayaran</label>
                                    <input
                                        type="number"
                                        required
                                        value={createForm.amount}
                                        onChange={(e) => setCreateForm((prev) => ({ ...prev, amount: e.target.value }))}
                                        className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    {createErrors.amount && (
                                        <p className="text-xs text-red-600 mt-1">{createErrors.amount[0]}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 block mb-1">Metode Pembayaran</label>
                                    <select
                                        value={createForm.payment_method}
                                        onChange={(e) =>
                                            setCreateForm((prev) => ({
                                                ...prev,
                                                payment_method: e.target.value,
                                            }))
                                        }
                                        className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="virtual_account">Virtual Account</option>
                                        <option value="qris">QRIS</option>
                                        <option value="credit_card">Credit Card / Snap</option>
                                        <option value="e_wallet">E-Wallet / Snap</option>
                                    </select>
                                    {createErrors.payment_method && (
                                        <p className="text-xs text-red-600 mt-1">{createErrors.payment_method[0]}</p>
                                    )}
                                </div>
                            </div>

                            {createForm.payment_method === "virtual_account" && (
                                <div>
                                    <label className="text-sm font-medium text-gray-700 block mb-1">Bank Virtual Account</label>
                                    <select
                                        value={createForm.bank}
                                        onChange={(e) => setCreateForm((prev) => ({ ...prev, bank: e.target.value }))}
                                        className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        {bankOptions.map((bank) => (
                                            <option key={bank.value} value={bank.value}>
                                                {bank.label}
                                            </option>
                                        ))}
                                    </select>
                                    {createErrors.bank && (
                                        <p className="text-xs text-red-600 mt-1">{createErrors.bank[0]}</p>
                                    )}
                                </div>
                            )}

                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">Catatan (opsional)</label>
                                <textarea
                                    rows="3"
                                    value={createForm.notes}
                                    onChange={(e) => setCreateForm((prev) => ({ ...prev, notes: e.target.value }))}
                                    className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                {createErrors.notes && (
                                    <p className="text-xs text-red-600 mt-1">{createErrors.notes[0]}</p>
                                )}
                            </div>

                            <div className="flex justify-end gap-3 pt-3">
                                <button
                                    type="button"
                                    onClick={() => setShowCreate(false)}
                                    className="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={creating}
                                    className="px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 disabled:opacity-60"
                                >
                                    {creating ? "Membuat..." : "Buat Pembayaran"}
                                </button>
                            </div>

                            {paymentInfo && (
                                <div className="mt-4 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 space-y-2">
                                    <p className="font-semibold">Pembayaran berhasil dibuat!</p>
                                    <p>Berikan informasi berikut kepada customer:</p>
                                    {paymentInfo.va_number && (
                                        <div className="flex items-center justify-between bg-white rounded-lg border border-green-100 px-3 py-2">
                                            <div>
                                                <p className="text-xs text-gray-500 uppercase">VA ({paymentInfo.bank?.toUpperCase()})</p>
                                                <p className="text-lg font-bold text-gray-900">{paymentInfo.va_number}</p>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => copyToClipboard(paymentInfo.va_number)}
                                                className="p-2 rounded-md border border-gray-200 hover:bg-gray-100"
                                            >
                                                <ClipboardDocumentIcon className="w-5 h-5 text-gray-600" />
                                            </button>
                                        </div>
                                    )}
                                    {paymentInfo.qris_url && (
                                        <a
                                            href={paymentInfo.qris_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg"
                                        >
                                            Lihat QRIS
                                        </a>
                                    )}
                                    {paymentInfo.redirect_url && (
                                        <a
                                            href={paymentInfo.redirect_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg"
                                        >
                                            Buka Snap Checkout
                                        </a>
                                    )}
                                </div>
                            )}
                        </form>
                    </div>
                </div>
            )}
            {showVerify && selectedPayment && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 flex items-center justify-center px-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
                        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 className="text-xl font-semibold text-gray-900">Verifikasi Pembayaran</h3>
                                <p className="text-sm text-gray-500">
                                    {selectedPayment.payment_code} � {formatCurrency(selectedPayment.amount)}
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={() => setShowVerify(false)}
                                className="p-2 rounded-full hover:bg-gray-100"
                            >
                                <XMarkIcon className="w-5 h-5 text-gray-500" />
                            </button>
                        </div>

                        <form onSubmit={handleVerify} className="p-6 space-y-4">
                            {verifyErrors.general && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                                    {verifyErrors.general}
                                </div>
                            )}

                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">Status Pembayaran</label>
                                <select
                                    value={verifyForm.status}
                                    onChange={(e) => setVerifyForm((prev) => ({ ...prev, status: e.target.value }))}
                                    className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="settlement">Settlement</option>
                                    <option value="capture">Capture</option>
                                    <option value="deny">Deny</option>
                                    <option value="cancel">Cancel</option>
                                </select>
                                {verifyErrors.status && (
                                    <p className="text-xs text-red-600 mt-1">{verifyErrors.status[0]}</p>
                                )}
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">Catatan (opsional)</label>
                                <textarea
                                    rows="3"
                                    value={verifyForm.notes}
                                    onChange={(e) => setVerifyForm((prev) => ({ ...prev, notes: e.target.value }))}
                                    className="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div className="flex justify-end gap-3 pt-3">
                                <button
                                    type="button"
                                    onClick={() => setShowVerify(false)}
                                    className="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={verifying}
                                    className="px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 disabled:opacity-60"
                                >
                                    {verifying ? "Memverifikasi..." : "Simpan Status"}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
