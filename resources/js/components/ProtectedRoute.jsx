import { Navigate } from 'react-router-dom';
import { useAuthStore } from '../stores/authStore';

const hasPermission = (user, permission) => {
    if (!permission) return true;
    if (!user) return false;

    const superRole = (user.role || user.roles?.[0]?.name || '').toLowerCase();
    if (superRole === 'superuser') {
        return true;
    }

    const permissions = user.permissions || [];
    return permissions.includes(permission);
};

export default function ProtectedRoute({ children, permission }) {
    const { isAuthenticated, user } = useAuthStore();

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    if (!hasPermission(user, permission)) {
        return <Navigate to="/dashboard" replace />;
    }

    return children;
}
