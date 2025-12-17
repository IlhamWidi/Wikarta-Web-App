<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Customer permissions
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            
            // Package permissions
            'view_packages',
            'create_packages',
            'edit_packages',
            'delete_packages',
            
            // Invoice permissions
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'void_invoices',
            'refund_invoices',
            
            // Payment permissions
            'view_payments',
            'create_payments',
            'edit_payments',
            'verify_payments',
            
            // Support ticket permissions
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'delete_tickets',
            'assign_tickets',
            
            // Installation permissions
            'view_installations',
            'create_installations',
            'edit_installations',
            'delete_installations',
            'assign_installations',
            
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Role management
            'manage_roles',
            
            // Reports
            'view_reports',
            'view_audit_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // 1. Superuser role - has all permissions
        $superuser = Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'web']);
        $superuser->givePermissionTo(Permission::all());

        // 2. Admin role - has most permissions except user/role management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers',
            'view_packages', 'create_packages', 'edit_packages', 'delete_packages',
            'view_invoices', 'create_invoices', 'edit_invoices', 'delete_invoices', 'void_invoices', 'refund_invoices',
            'view_payments', 'create_payments', 'edit_payments', 'verify_payments',
            'view_tickets', 'create_tickets', 'edit_tickets', 'delete_tickets', 'assign_tickets',
            'view_installations', 'create_installations', 'edit_installations', 'delete_installations', 'assign_installations',
            'view_reports', 'view_audit_logs',
        ]);

        // 3. Marketing role - customer and package management
        $marketing = Role::firstOrCreate(['name' => 'marketing', 'guard_name' => 'web']);
        $marketing->givePermissionTo([
            'view_customers', 'create_customers', 'edit_customers',
            'view_packages',
            'view_invoices',
            'view_tickets', 'create_tickets',
            'view_installations',
        ]);

        // 4. Finance role - invoice and payment management
        $finance = Role::firstOrCreate(['name' => 'finance', 'guard_name' => 'web']);
        $finance->givePermissionTo([
            'view_customers',
            'view_packages',
            'view_invoices', 'create_invoices', 'edit_invoices', 'void_invoices', 'refund_invoices',
            'view_payments', 'create_payments', 'verify_payments',
            'view_reports',
        ]);

        // 5. Support role - ticket and customer support
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $support->givePermissionTo([
            'view_customers', 'edit_customers',
            'view_tickets', 'create_tickets', 'edit_tickets', 'assign_tickets',
            'view_installations',
        ]);

        // 6. Technician role - installation management
        $technician = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
        $technician->givePermissionTo([
            'view_customers',
            'view_installations', 'edit_installations',
            'view_tickets', 'edit_tickets',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created roles: superuser, admin, marketing, finance, support, technician');
    }
}
