import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeftIcon, ShieldCheckIcon, CheckIcon } from '@heroicons/react/24/outline';
import DashboardLayout from '../../layouts/DashboardLayout';
import api from '../../utils/api';
import toast from 'react-hot-toast';

const RoleForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEdit = !!id;

  const [formData, setFormData] = useState({
    name: '',
    permissions: []
  });

  const [allPermissions, setAllPermissions] = useState({});
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(isEdit);

  useEffect(() => {
    fetchPermissions();
    if (isEdit) {
      fetchRole();
    }
  }, [id]);

  const fetchPermissions = async () => {
    try {
      const response = await api.get('/roles-permissions');
      setAllPermissions(response.data.data);
    } catch (error) {
      console.error('Error fetching permissions:', error);
      toast.error('Gagal memuat permissions');
    }
  };

  const fetchRole = async () => {
    try {
      setLoadingData(true);
      const response = await api.get(`/roles/${id}`);
      const role = response.data.data;
      setFormData({
        name: role.name,
        permissions: role.permissions.map(p => p.name)
      });
    } catch (error) {
      console.error('Error fetching role:', error);
      toast.error('Gagal memuat data role');
    } finally {
      setLoadingData(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      if (isEdit) {
        await api.put(`/roles/${id}`, formData);
        toast.success('Role berhasil diupdate');
      } else {
        await api.post('/roles', formData);
        toast.success('Role berhasil dibuat');
      }
      navigate('/roles');
    } catch (error) {
      const message = error.response?.data?.message || 'Gagal menyimpan role';
      toast.error(message);
      console.error('Error saving role:', error);
    } finally {
      setLoading(false);
    }
  };

  const handlePermissionToggle = (permissionName) => {
    setFormData(prev => ({
      ...prev,
      permissions: prev.permissions.includes(permissionName)
        ? prev.permissions.filter(p => p !== permissionName)
        : [...prev.permissions, permissionName]
    }));
  };

  const handleGroupToggle = (groupPermissions) => {
    const allSelected = groupPermissions.every(p => formData.permissions.includes(p.name));
    
    setFormData(prev => ({
      ...prev,
      permissions: allSelected
        ? prev.permissions.filter(p => !groupPermissions.find(gp => gp.name === p))
        : [...new Set([...prev.permissions, ...groupPermissions.map(p => p.name)])]
    }));
  };

  if (loadingData) {
    return (
      <DashboardLayout>
        <div className="p-12 text-center">
          <div className="inline-block w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
          <p className="mt-4 text-gray-600">Loading...</p>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <div className="space-y-6">
        {/* Header */}
        <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate('/roles')}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ArrowLeftIcon className="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h1 className="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                {isEdit ? 'Edit Role' : 'Tambah Role Baru'}
              </h1>
              <p className="text-gray-600 mt-1">Kelola role dan permission</p>
            </div>
          </div>
        </div>

        {/* Form */}
        <div className="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Role Name */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Role Name <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="e.g., manager, supervisor"
                required
              />
              <p className="mt-1 text-xs text-gray-500">
                Gunakan huruf kecil tanpa spasi
              </p>
            </div>

            {/* Permissions */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-4">
                Permissions
              </label>
              <div className="space-y-4">
                {Object.entries(allPermissions).map(([group, permissions]) => {
                  const allSelected = permissions.every(p => formData.permissions.includes(p.name));
                  const someSelected = permissions.some(p => formData.permissions.includes(p.name)) && !allSelected;
                  
                  return (
                    <div key={group} className="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                      <div className="flex items-center gap-3 mb-3">
                        <button
                          type="button"
                          onClick={() => handleGroupToggle(permissions)}
                          className={`w-5 h-5 rounded border-2 flex items-center justify-center transition-all ${
                            allSelected 
                              ? 'bg-gradient-to-r from-purple-600 to-pink-600 border-purple-600' 
                              : someSelected
                              ? 'bg-purple-200 border-purple-400'
                              : 'border-gray-300 hover:border-purple-400'
                          }`}
                        >
                          {allSelected && <CheckIcon className="w-3 h-3 text-white" />}
                          {someSelected && <div className="w-2 h-0.5 bg-purple-600" />}
                        </button>
                        <ShieldCheckIcon className="w-5 h-5 text-purple-600" />
                        <span className="font-semibold text-gray-800 capitalize">
                          {group.replace('_', ' ')}
                        </span>
                        <span className="ml-auto text-xs text-gray-500">
                          {permissions.filter(p => formData.permissions.includes(p.name)).length}/{permissions.length}
                        </span>
                      </div>
                      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 ml-8">
                        {permissions.map((permission) => (
                          <label
                            key={permission.name}
                            className="flex items-center gap-2 p-2 hover:bg-white/50 rounded-lg cursor-pointer transition-colors"
                          >
                            <input
                              type="checkbox"
                              checked={formData.permissions.includes(permission.name)}
                              onChange={() => handlePermissionToggle(permission.name)}
                              className="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            />
                            <span className="text-sm text-gray-700">
                              {permission.name.replace(/_/g, ' ')}
                            </span>
                          </label>
                        ))}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Actions */}
            <div className="flex items-center gap-3 pt-4 border-t">
              <button
                type="submit"
                disabled={loading}
                className="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? (
                  <span className="flex items-center justify-center gap-2">
                    <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    Menyimpan...
                  </span>
                ) : (
                  isEdit ? 'Update Role' : 'Simpan Role'
                )}
              </button>
              <button
                type="button"
                onClick={() => navigate('/roles')}
                className="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </DashboardLayout>
  );
};

export default RoleForm;
