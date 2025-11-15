import { useState, useEffect } from 'react';
import { PlusIcon, PencilIcon, TrashIcon, MagnifyingGlassIcon, UserCircleIcon } from '@heroicons/react/24/outline';
import DashboardLayout from '../../layouts/DashboardLayout';
import api from '../../utils/api';
import toast from 'react-hot-toast';

export default function UserManagement() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [currentUser, setCurrentUser] = useState(null);
    const [availableRoles, setAvailableRoles] = useState([]);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        role: 'marketing'
    });

    useEffect(() => {
        fetchUsers();
        fetchRoles();
    }, []);

    const fetchUsers = async () => {
        try {
            setLoading(true);
            const response = await api.get('/users');
            setUsers(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch users:', error);
            toast.error('Gagal memuat data user');
        } finally {
            setLoading(false);
        }
    };

    const fetchRoles = async () => {
        try {
            const response = await api.get('/roles');
            setAvailableRoles(response.data.data || response.data);
        } catch (error) {
            console.error('Failed to fetch roles:', error);
        }
    };

    const handleCreate = () => {
        setEditMode(false);
        setCurrentUser(null);
        setFormData({
            name: '',
            email: '',
            password: '',
            role: 'marketing'
        });
        setShowModal(true);
    };

    const handleEdit = (user) => {
        setEditMode(true);
        setCurrentUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            password: '',
            role: user.roles?.[0]?.name || 'marketing'
        });
        setShowModal(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        try {
            if (editMode) {
                // Update user
                await api.put(`/users/${currentUser.id}`, formData);
                toast.success('User berhasil diupdate');
            } else {
                // Create user
                await api.post('/users', formData);
                toast.success('User berhasil dibuat');
            }
            setShowModal(false);
            fetchUsers();
        } catch (error) {
            const message = error.response?.data?.message || 'Gagal menyimpan user';
            toast.error(message);
        }
    };

    const handleChangeRole = async (userId, newRole) => {
        if (!confirm(`Ubah role user ini menjadi ${newRole}?`)) return;

        try {
            console.log('Changing role:', { user_id: userId, role: newRole });
            const response = await api.post('/assign-role', {
                user_id: userId,
                role: newRole
            });
            console.log('Response:', response.data);
            toast.success('Role berhasil diubah');
            fetchUsers();
        } catch (error) {
            console.error('Error changing role:', error.response || error);
            const message = error.response?.data?.message || 'Gagal mengubah role';
            toast.error(message);
        }
    };

    const handleDelete = async (id) => {
        if (!confirm('Yakin ingin menghapus user ini?')) return;

        try {
            await api.delete(`/users/${id}`);
            toast.success('User berhasil dihapus');
            fetchUsers();
        } catch (error) {
            toast.error('Gagal menghapus user');
        }
    };

    const filteredUsers = users.filter(user => 
        user.name?.toLowerCase().includes(search.toLowerCase()) ||
        user.email?.toLowerCase().includes(search.toLowerCase())
    );

    const roleColors = {
        superuser: 'bg-purple-100 text-purple-800',
        keuangan: 'bg-blue-100 text-blue-800',
        marketing: 'bg-green-100 text-green-800',
        teknisi: 'bg-orange-100 text-orange-800'
    };

    return (
        <DashboardLayout>
            <div className="space-y-6">
                {/* Header */}
                <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h1 className="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                User Management
                            </h1>
                            <p className="text-gray-600 mt-1">Kelola user dan assign role</p>
                        </div>
                        <button
                            onClick={handleCreate}
                            className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all"
                        >
                            <PlusIcon className="w-5 h-5" />
                            Tambah User
                        </button>
                    </div>

                    {/* Search */}
                    <div className="relative">
                        <MagnifyingGlassIcon className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari nama atau email..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        />
                    </div>

                    <div className="mt-4 text-sm text-gray-600">
                        Menampilkan {filteredUsers.length} dari {users.length} users
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
                                        <th className="px-6 py-4 text-left text-sm font-semibold">User</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Email</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Current Role</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Change Role</th>
                                        <th className="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100">
                                    {filteredUsers.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                                                <UserCircleIcon className="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                                <p>Tidak ada data user</p>
                                            </td>
                                        </tr>
                                    ) : (
                                        filteredUsers.map((user, index) => (
                                            <tr 
                                                key={user.id}
                                                className={`hover:bg-purple-50/50 transition-colors ${
                                                    index % 2 === 0 ? 'bg-white/50' : 'bg-gray-50/50'
                                                }`}
                                            >
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                                            {user.name?.[0]?.toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="font-semibold text-gray-800">{user.name}</p>
                                                            <p className="text-xs text-gray-500">ID: {user.id}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-gray-600">
                                                    {user.email}
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium capitalize ${
                                                        roleColors[user.roles?.[0]?.name] || 'bg-gray-100 text-gray-800'
                                                    }`}>
                                                        {user.roles?.[0]?.name || 'No Role'}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <select
                                                        value={user.roles?.[0]?.name || 'marketing'}
                                                        onChange={(e) => handleChangeRole(user.id, e.target.value)}
                                                        className="px-3 py-1 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                    >
                                                        {availableRoles.map(role => (
                                                            <option key={role.id} value={role.name}>
                                                                {role.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <button
                                                            onClick={() => handleEdit(user)}
                                                            className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                            title="Edit"
                                                        >
                                                            <PencilIcon className="w-5 h-5" />
                                                        </button>
                                                        {user.roles?.[0]?.name !== 'superuser' && (
                                                            <button
                                                                onClick={() => handleDelete(user.id)}
                                                                className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                                title="Delete"
                                                            >
                                                                <TrashIcon className="w-5 h-5" />
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

            {/* Modal Create/Edit User */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-4">
                            {editMode ? 'Edit User' : 'Tambah User Baru'}
                        </h2>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={formData.email}
                                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    required
                                />
                            </div>
                            {!editMode && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Password
                                    </label>
                                    <input
                                        type="password"
                                        value={formData.password}
                                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required={!editMode}
                                        minLength="8"
                                    />
                                </div>
                            )}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Role
                                </label>
                                <select
                                    value={formData.role}
                                    onChange={(e) => setFormData({ ...formData, role: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                    {availableRoles.map(role => (
                                        <option key={role.id} value={role.name}>
                                            {role.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    className="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all"
                                >
                                    {editMode ? 'Update' : 'Simpan'}
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                                >
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
