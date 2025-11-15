<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Customer Management
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            
            // Package Management
            'view_packages',
            'create_packages',
            'edit_packages',
            'delete_packages',
            
            // Invoice Management
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'void_invoices',
            
            // Payment Management
            'view_payments',
            'create_payments',
            'verify_payments',
            'refund_payments',
            
            // Financial Operations
            'view_reports',
            'manage_reconciliation',
            'approve_refunds',
            'approve_voids',
            
            // Support Tickets
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'assign_tickets',
            'resolve_tickets',
            
            // Installation Management
            'view_installations',
            'create_installations',
            'edit_installations',
            'assign_installations',
            'complete_installations',
            
            // Giveaway Management
            'view_giveaways',
            'create_giveaways',
            'edit_giveaways',
            'approve_giveaways',
            
            // Attendance
            'view_attendance',
            'mark_attendance',
            'manage_attendance',
            
            // CMS
            'view_cms',
            'manage_cms',
            
            // User & Role Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',
            
            // Audit Logs
            'view_audit_logs',
            
            // Webhook Management
            'view_webhooks',
            'manage_webhooks',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions

        // 1. SUPERUSER - Full system access
        $superuser = Role::firstOrCreate(['name' => 'superuser']);
        $superuser->syncPermissions(Permission::all());

        // 2. KEUANGAN - Financial operations
        $keuangan = Role::firstOrCreate(['name' => 'keuangan']);
        $keuangan->syncPermissions([
            'view_customers',
            'view_packages',
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'void_invoices',
            'view_payments',
            'create_payments',
            'verify_payments',
            'refund_payments',
            'view_reports',
            'manage_reconciliation',
            'approve_refunds',
            'approve_voids',
            'view_tickets',
            'create_tickets',
            'view_audit_logs',
            'view_webhooks',
            'manage_webhooks',
        ]);

        // 3. MARKETING - Customer & package management, promotions
        $marketing = Role::firstOrCreate(['name' => 'marketing']);
        $marketing->syncPermissions([
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'view_packages',
            'create_packages',
            'edit_packages',
            'view_invoices',
            'view_payments',
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'view_giveaways',
            'create_giveaways',
            'edit_giveaways',
            'approve_giveaways',
            'view_cms',
            'manage_cms',
        ]);

        // 4. TEKNISI - Technical support and installations
        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);
        $teknisi->syncPermissions([
            'view_customers',
            'view_packages',
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'assign_tickets',
            'resolve_tickets',
            'view_installations',
            'create_installations',
            'edit_installations',
            'complete_installations',
            'view_attendance',
            'mark_attendance',
        ]);
    }
}
