<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InstallationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RoleController;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans']);

// Public packages (for landing page)
Route::get('/packages/public', [PackageController::class, 'publicIndex']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Customers
    Route::apiResource('customers', CustomerController::class);
    
    // Packages
    Route::apiResource('packages', PackageController::class);
    
    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void']);
    Route::post('/invoices/{invoice}/refund', [InvoiceController::class, 'refund']);
    
    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);

    // Users helper
    Route::get('/users/options', [UserController::class, 'options']);
    
    // User Management (superuser only)
    Route::apiResource('users', UserController::class)->except(['options']);

    // Support tickets
    Route::apiResource('tickets', SupportTicketController::class);
    Route::post('/tickets/{ticket}/assign', [SupportTicketController::class, 'assign']);
    Route::post('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus']);

    // Installations
    Route::apiResource('installations', InstallationController::class);
    Route::post('/installations/{installation}/assign', [InstallationController::class, 'assignTechnician']);
    Route::post('/installations/{installation}/status', [InstallationController::class, 'updateStatus']);

    // Notifications / dunning logs
    Route::get('/notifications/logs', [NotificationController::class, 'index']);
    Route::post('/notifications/{notificationLog}/resend', [NotificationController::class, 'resend']);

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show']);

    // Role Management (superuser only)
    Route::apiResource('roles', RoleController::class);
    Route::get('/roles-permissions', [RoleController::class, 'permissions']);
    Route::post('/assign-role', [RoleController::class, 'assignRole']);
});
