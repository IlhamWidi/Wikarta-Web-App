import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { Routes, Route } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import LandingPage from './pages/LandingPage';
import Login from './pages/Auth/Login';
import Register from './pages/Auth/Register';
import Dashboard from './pages/Dashboard/Dashboard';
import Customers from './pages/Dashboard/Customers';
import Packages from './pages/Dashboard/Packages';
import Invoices from './pages/Dashboard/Invoices';
import Payments from './pages/Dashboard/Payments';
import AuditLogs from './pages/Dashboard/AuditLogs';
import SupportTickets from './pages/Dashboard/SupportTickets';
import Installations from './pages/Dashboard/Installations';
import Notifications from './pages/Dashboard/Notifications';
import RoleList from './pages/Roles/RoleList';
import RoleForm from './pages/Roles/RoleForm';
import UserManagement from './pages/Users/UserManagement';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
    return (
        <div className="min-h-screen">
            <Toaster position="top-right" />
            <Routes>
                {/* Public Routes */}
                <Route path="/" element={<LandingPage />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />

                {/* Protected Routes */}
                <Route 
                    path="/dashboard" 
                    element={
                        <ProtectedRoute>
                            <Dashboard />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/customers" 
                    element={
                        <ProtectedRoute permission="view_customers">
                            <Customers />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/packages" 
                    element={
                        <ProtectedRoute permission="view_packages">
                            <Packages />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/invoices" 
                    element={
                        <ProtectedRoute permission="view_invoices">
                            <Invoices />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/payments" 
                    element={
                        <ProtectedRoute permission="view_payments">
                            <Payments />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/tickets" 
                    element={
                        <ProtectedRoute permission="view_tickets">
                            <SupportTickets />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/installations" 
                    element={
                        <ProtectedRoute permission="view_installations">
                            <Installations />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/notifications" 
                    element={
                        <ProtectedRoute permission="view_reports">
                            <Notifications />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/audit-logs" 
                    element={
                        <ProtectedRoute permission="view_audit_logs">
                            <AuditLogs />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/roles" 
                    element={
                        <ProtectedRoute permission="manage_roles">
                            <UserManagement />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/roles/create" 
                    element={
                        <ProtectedRoute permission="manage_roles">
                            <RoleForm />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/roles/:id/edit" 
                    element={
                        <ProtectedRoute permission="manage_roles">
                            <RoleForm />
                        </ProtectedRoute>
                    } 
                />
                
                {/* 404 - Redirect to Landing */}
                <Route path="*" element={<LandingPage />} />
            </Routes>
        </div>
    );
}

// Mount React app
const root = createRoot(document.getElementById('app'));
root.render(
    <React.StrictMode>
        <BrowserRouter>
            <App />
        </BrowserRouter>
    </React.StrictMode>
);
