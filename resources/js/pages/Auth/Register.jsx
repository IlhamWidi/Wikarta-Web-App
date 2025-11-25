import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuthStore } from "../../stores/authStore";
import api from "../../utils/api";

const initialForm = {
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
};

export default function Register() {
    const navigate = useNavigate();
    const setAuth = useAuthStore((state) => state.setAuth);

    const [formData, setFormData] = useState(initialForm);
    const [fieldErrors, setFieldErrors] = useState({});
    const [generalError, setGeneralError] = useState("");
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
        setFieldErrors({ ...fieldErrors, [e.target.name]: null });
        setGeneralError("");
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setLoading(true);
        setGeneralError("");
        setFieldErrors({});

        try {
            const response = await api.post("/auth/register", formData);
            const { user, token } = response.data;
            setAuth(user, token);
            navigate("/dashboard");
        } catch (error) {
            if (error.response?.status === 422) {
                setFieldErrors(error.response.data.errors || {});
            } else {
                setGeneralError(error.response?.data?.message || "Registrasi gagal. Coba lagi.");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
            <div className="absolute inset-0 pointer-events-none">
                <div className="absolute top-10 left-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl animate-pulse" />
                <div className="absolute bottom-10 right-10 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-pulse" />
            </div>

            <div className="relative z-10 w-full max-w-lg px-6">
                <div className="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                    <div className="text-center mb-8">
                        <h1 className="text-4xl font-bold text-white mb-2">Daftar Akun</h1>
                        <p className="text-indigo-100">Gabung ke Wikarta untuk kelola pelanggan & pembayaran.</p>
                    </div>

                    {generalError && (
                        <div className="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-200 text-sm">
                            {generalError}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-white text-sm font-medium mb-2">Nama Lengkap</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                required
                                className="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Nama Anda"
                            />
                            {fieldErrors.name && <p className="text-xs text-rose-200 mt-1">{fieldErrors.name[0]}</p>}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-white text-sm font-medium mb-2">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    value={formData.email}
                                    onChange={handleChange}
                                    required
                                    className="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="email@domain.com"
                                />
                                {fieldErrors.email && <p className="text-xs text-rose-200 mt-1">{fieldErrors.email[0]}</p>}
                            </div>
                            <div>
                                <label className="block text-white text-sm font-medium mb-2">Nomor Telepon</label>
                                <input
                                    type="text"
                                    name="phone"
                                    value={formData.phone}
                                    onChange={handleChange}
                                    required
                                    className="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="08xxxxxxxxxx"
                                />
                                {fieldErrors.phone && <p className="text-xs text-rose-200 mt-1">{fieldErrors.phone[0]}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-white text-sm font-medium mb-2">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    value={formData.password}
                                    onChange={handleChange}
                                    required
                                    className="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Minimal 8 karakter"
                                />
                                {fieldErrors.password && <p className="text-xs text-rose-200 mt-1">{fieldErrors.password[0]}</p>}
                            </div>
                            <div>
                                <label className="block text-white text-sm font-medium mb-2">Konfirmasi Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    value={formData.password_confirmation}
                                    onChange={handleChange}
                                    required
                                    className="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Ulangi password"
                                />
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-purple-900 transition disabled:opacity-60"
                        >
                            {loading ? "Mendaftarkan..." : "Daftar Sekarang"}
                        </button>
                    </form>

                    <div className="mt-6 text-center space-y-2 text-sm">
                        <p className="text-indigo-100">
                            Sudah punya akun? {" "}
                            <Link to="/login" className="text-white font-semibold hover:underline">
                                Masuk di sini
                            </Link>
                        </p>
                        <p>
                            <Link to="/" className="text-indigo-200 hover:text-white">
                                ? Kembali ke beranda
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
