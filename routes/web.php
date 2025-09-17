<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryStockController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceSettingController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OmzetController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController as MasterProduct;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransferStockController;
use App\Http\Controllers\Website\FaqController;
use App\Http\Controllers\Website\MethodCotroller;
use App\Http\Controllers\Website\PartnerController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasanganController;
use App\Http\Controllers\GiveAwayController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\KurirGiveawayController;
use App\Http\Controllers\OmzetStatisticController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ResolveTicketController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceMonitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/job/generate-invoice', [JobController::class, 'generate_invoice'])->name('job.generate-invoice');
Route::get('/job/generate-notification', [JobController::class, 'generate_notification'])->name('job.generate-notification');

Route::get('/landing/payment/{id}', [LandingController::class, 'payment'])->name('landing.payment');
Route::post('/landing/process-payment/{id}', [LandingController::class, 'process_payment'])->name('landing.process-payment');
Route::get('/landing/process-waiting/{id}/{method}', [LandingController::class, 'waiting_payment'])->name('landing.waiting-payment');

Route::get('/invoice-task', [ScheduleController::class, 'invoice_task'])->name('invoice-task');
Route::get('/invoice-notification', [ScheduleController::class, 'invoice_notification'])->name('invoice-notification');
Route::get('/midtrans-notification', [MidtransController::class, 'notification_handler'])->name('notification-handler');
Route::post('/midtrans-notification', [MidtransController::class, 'notification_handler'])->name('notification-handler');
Route::get('/invoice/print/{id}', [InvoiceController::class, 'print'])->name('invoice.print');

Route::get('/', [AuthController::class, 'index'])->name('root');

Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/user-admin', [UserController::class, 'index'])->name('user-admin')->middleware('role:SUPERUSER');
    Route::get('/user-admin/create', [UserController::class, 'create'])->name('user-admin.create')->middleware('role:SUPERUSER');
    Route::post('/user-admin/store', [UserController::class, 'store'])->name('user-admin.store')->middleware('role:SUPERUSER');
    Route::get('/user-admin/edit/{id}', [UserController::class, 'edit'])->name('user-admin.edit')->middleware('role:SUPERUSER');
    Route::post('/user-admin/update/{id}', [UserController::class, 'update'])->name('user-admin.update')->middleware('role:SUPERUSER');
    Route::post('/user-admin/delete', [UserController::class, 'delete'])->name('user-admin.delete')->middleware('role:SUPERUSER');

    Route::get('/user-customer', [CustomerController::class, 'index'])->name('user-customer')->middleware('role:SUPERUSER');
    Route::get('/user-customer/create', [CustomerController::class, 'create'])->name('user-customer.create')->middleware('role:SUPERUSER');
    Route::post('/user-customer/store', [CustomerController::class, 'store'])->name('user-customer.store')->middleware('role:SUPERUSER');
    Route::get('/user-customer/edit/{id}', [CustomerController::class, 'edit'])->name('user-customer.edit')->middleware('role:SUPERUSER');
    Route::get('/user-customer/detail/{id}', [CustomerController::class, 'detail'])->name('user-customer.detail')->middleware('role:SUPERUSER');
    Route::post('/user-customer/update/{id}', [CustomerController::class, 'update'])->name('user-customer.update')->middleware('role:SUPERUSER');
    Route::post('/user-customer/delete', [CustomerController::class, 'delete'])->name('user-customer.delete')->middleware('role:SUPERUSER');

    Route::get('/package', [PackageController::class, 'index'])->name('package')->middleware('role:SUPERUSER');
    Route::get('/package/create', [PackageController::class, 'create'])->name('package.create')->middleware('role:SUPERUSER');
    Route::post('/package/store', [PackageController::class, 'store'])->name('package.store')->middleware('role:SUPERUSER');
    Route::get('/package/edit/{id}', [PackageController::class, 'edit'])->name('package.edit')->middleware('role:SUPERUSER');
    Route::post('/package/update/{id}', [PackageController::class, 'update'])->name('package.update')->middleware('role:SUPERUSER');
    Route::post('/package/delete', [PackageController::class, 'delete'])->name('package.delete')->middleware('role:SUPERUSER');
    Route::post('/package/show', [PackageController::class, 'show'])->name('package.show')->middleware('role:SUPERUSER');

    Route::get('/product', [MasterProduct::class, 'index'])->name('product')->middleware('role:SUPERUSER');
    Route::get('/product/create', [MasterProduct::class, 'create'])->name('product.create')->middleware('role:SUPERUSER');
    Route::post('/product/store', [MasterProduct::class, 'store'])->name('product.store')->middleware('role:SUPERUSER');
    Route::get('/product/edit/{id}', [MasterProduct::class, 'edit'])->name('product.edit')->middleware('role:SUPERUSER');
    Route::post('/product/update/{id}', [MasterProduct::class, 'update'])->name('product.update')->middleware('role:SUPERUSER');
    Route::post('/product/delete', [MasterProduct::class, 'delete'])->name('product.delete')->middleware('role:SUPERUSER');

    Route::get('/branch', [BranchController::class, 'index'])->name('branch')->middleware('role:SUPERUSER');
    Route::get('/branch/create', [BranchController::class, 'create'])->name('branch.create')->middleware('role:SUPERUSER');
    Route::post('/branch/store', [BranchController::class, 'store'])->name('branch.store')->middleware('role:SUPERUSER');
    Route::get('/branch/edit/{id}', [BranchController::class, 'edit'])->name('branch.edit')->middleware('role:SUPERUSER');
    Route::post('/branch/update/{id}', [BranchController::class, 'update'])->name('branch.update')->middleware('role:SUPERUSER');
    Route::post('/branch/delete', [BranchController::class, 'delete'])->name('branch.delete')->middleware('role:SUPERUSER');

    Route::get('/inventorystock', [InventoryStockController::class, 'index'])->name('inventorystock')->middleware('role:SUPERUSER');
    Route::get('/inventorystock/create', [InventoryStockController::class, 'create'])->name('inventorystock.create')->middleware('role:SUPERUSER');
    Route::post('/inventorystock/store', [InventoryStockController::class, 'store'])->name('inventorystock.store')->middleware('role:SUPERUSER');
    Route::get('/inventorystock/edit/{id}', [InventoryStockController::class, 'edit'])->name('inventorystock.edit')->middleware('role:SUPERUSER');
    Route::post('/inventorystock/update/{id}', [InventoryStockController::class, 'update'])->name('inventorystock.update')->middleware('role:SUPERUSER');
    Route::post('/inventorystock/delete', [InventoryStockController::class, 'delete'])->name('inventorystock.delete')->middleware('role:SUPERUSER');
    Route::get('/inventorystock/export', [InventoryStockController::class, 'export'])->name('inventorystock.export')->middleware('role:SUPERUSER');

    Route::get('/order', [OrderController::class, 'index'])->name('order')->middleware('role:SUPERUSER');
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create')->middleware('role:SUPERUSER');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store')->middleware('role:SUPERUSER');
    Route::get('/order/edit/{id}', [OrderController::class, 'edit'])->name('order.edit')->middleware('role:SUPERUSER');
    Route::post('/order/update/{id}', [OrderController::class, 'update'])->name('order.update')->middleware('role:SUPERUSER');
    Route::post('/order/delete', [OrderController::class, 'delete'])->name('order.delete')->middleware('role:SUPERUSER');
    Route::get('/order/export', [OrderController::class, 'export'])->name('order.export')->middleware('role:SUPERUSER');

    Route::get('/retur', [ReturController::class, 'index'])->name('retur')->middleware('role:SUPERUSER');
    Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create')->middleware('role:SUPERUSER');
    Route::post('/retur/store', [ReturController::class, 'store'])->name('retur.store')->middleware('role:SUPERUSER');
    Route::get('/retur/edit/{id}', [ReturController::class, 'edit'])->name('retur.edit')->middleware('role:SUPERUSER');
    Route::post('/retur/update/{id}', [ReturController::class, 'update'])->name('retur.update')->middleware('role:SUPERUSER');
    Route::post('/retur/delete', [ReturController::class, 'delete'])->name('retur.delete')->middleware('role:SUPERUSER');

    Route::get('/category', [CategoryController::class, 'index'])->name('category')->middleware('role:SUPERUSER');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create')->middleware('role:SUPERUSER');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store')->middleware('role:SUPERUSER');
    Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit')->middleware('role:SUPERUSER');
    Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update')->middleware('role:SUPERUSER');
    Route::post('/category/delete', [CategoryController::class, 'delete'])->name('category.delete')->middleware('role:SUPERUSER');

    Route::get('/invoice-setting', [InvoiceSettingController::class, 'index'])->name('invoice-setting')->middleware('role:SUPERUSER');
    Route::post('/invoice-setting/store', [InvoiceSettingController::class, 'store'])->name('invoice-setting.store')->middleware('role:SUPERUSER');

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice')->middleware('role:SUPERUSER,MARKETING');
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create')->middleware('role:SUPERUSER');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store')->middleware('role:SUPERUSER');
    Route::post('/invoice', [InvoiceController::class, 'index'])->name('invoice')->middleware('role:SUPERUSER');
    Route::post('/invoice/edit', [InvoiceController::class, 'edit'])->name('invoice.edit')->middleware('role:SUPERUSER');
    Route::post('/invoice/update', [InvoiceController::class, 'update'])->name('invoice.update')->middleware('role:SUPERUSER,MARKETING');
    Route::get('/invoice/export', [InvoiceController::class, 'export'])->name('invoice.export');

    Route::get('/omzet', [OmzetController::class, 'index'])->name('omzet')->middleware('role:SUPERUSER');
    Route::post('/omzet', [OmzetController::class, 'index'])->name('omzet')->middleware('role:SUPERUSER');

    // Tambahkan route untuk statistik omzet
    Route::get('/omzet-statistic', [OmzetStatisticController::class, 'index'])->name('omzet-statistic.index');
    Route::get('/omzet-statistic/chart-data', [OmzetStatisticController::class, 'getChartData'])->name('omzet-statistic.chart-data');

    Route::get('/website-product', [ProductController::class, 'index'])->name('website-product')->middleware('role:SUPERUSER');
    Route::get('/website-product/create', [ProductController::class, 'create'])->name('website-product.create')->middleware('role:SUPERUSER');
    Route::post('/website-product/store', [ProductController::class, 'store'])->name('website-product.store')->middleware('role:SUPERUSER');
    Route::get('/website-product/edit/{id}', [ProductController::class, 'edit'])->name('website-product.edit')->middleware('role:SUPERUSER');
    Route::post('/website-product/update/{id}', [ProductController::class, 'update'])->name('website-product.update')->middleware('role:SUPERUSER');
    Route::post('/website-product/delete', [ProductController::class, 'delete'])->name('website-product.delete')->middleware('role:SUPERUSER');

    Route::get('/website-faq', [FaqController::class, 'index'])->name('website-faq')->middleware('role:SUPERUSER');
    Route::get('/website-faq/create', [FaqController::class, 'create'])->name('website-faq.create')->middleware('role:SUPERUSER');
    Route::post('/website-faq/store', [FaqController::class, 'store'])->name('website-faq.store')->middleware('role:SUPERUSER');
    Route::get('/website-faq/edit/{id}', [FaqController::class, 'edit'])->name('website-faq.edit')->middleware('role:SUPERUSER');
    Route::post('/website-faq/update/{id}', [FaqController::class, 'update'])->name('website-faq.update')->middleware('role:SUPERUSER');
    Route::post('/website-faq/delete', [FaqController::class, 'delete'])->name('website-faq.delete')->middleware('role:SUPERUSER');

    Route::get('/website-method', [MethodCotroller::class, 'index'])->name('website-method')->middleware('role:SUPERUSER');
    Route::get('/website-method/create', [MethodCotroller::class, 'create'])->name('website-method.create')->middleware('role:SUPERUSER');
    Route::post('/website-method/store', [MethodCotroller::class, 'store'])->name('website-method.store')->middleware('role:SUPERUSER');
    Route::get('/website-method/edit/{id}', [MethodCotroller::class, 'edit'])->name('website-method.edit')->middleware('role:SUPERUSER');
    Route::post('/website-method/update/{id}', [MethodCotroller::class, 'update'])->name('website-method.update')->middleware('role:SUPERUSER');
    Route::post('/website-method/delete', [MethodCotroller::class, 'delete'])->name('website-method.delete')->middleware('role:SUPERUSER');

    Route::get('/website-partner', [PartnerController::class, 'index'])->name('website-partner')->middleware('role:SUPERUSER');
    Route::get('/website-partner/create', [PartnerController::class, 'create'])->name('website-partner.create')->middleware('role:SUPERUSER');
    Route::post('/website-partner/store', [PartnerController::class, 'store'])->name('website-partner.store')->middleware('role:SUPERUSER');
    Route::get('/website-partner/edit/{id}', [PartnerController::class, 'edit'])->name('website-partner.edit')->middleware('role:SUPERUSER');
    Route::post('/website-partner/update/{id}', [PartnerController::class, 'update'])->name('website-partner.update')->middleware('role:SUPERUSER');
    Route::post('/website-partner/delete', [PartnerController::class, 'delete'])->name('website-partner.delete')->middleware('role:SUPERUSER');

    Route::get('/transfer-stock', [TransferStockController::class, 'index'])->name('transfer-stock')->middleware('role:SUPERUSER');
    Route::get('/transfer-stock/create', [TransferStockController::class, 'create'])->name('transfer-stock.create')->middleware('role:SUPERUSER');
    Route::post('/transfer-stock/store', [TransferStockController::class, 'store'])->name('transfer-stock.store')->middleware('role:SUPERUSER');
    Route::get('/transfer-stock/edit/{id}', [TransferStockController::class, 'edit'])->name('transfer-stock.edit')->middleware('role:SUPERUSER');
    Route::post('/transfer-stock/update/{id}', [TransferStockController::class, 'update'])->name('transfer-stock.update')->middleware('role:SUPERUSER');
    Route::post('/transfer-stock/delete', [TransferStockController::class, 'delete'])->name('transfer-stock.delete')->middleware('role:SUPERUSER');

    Route::get('/finance-report', [FinanceController::class, 'index'])->name('finance-report')->middleware('role:SUPERUSER');
    Route::post('/finance-report', [FinanceController::class, 'index'])->name('finance-report')->middleware('role:SUPERUSER');

    Route::get('/stock', [StockController::class, 'index'])->name('stock')->middleware('role:SUPERUSER');

    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan')->middleware('role:SUPERUSER,MARKETING,TEKNISI');
    Route::get('/pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create')->middleware('role:SUPERUSER,MARKETING,TEKNISI');
    Route::post('/pelanggan/store', [PelangganController::class, 'store'])->name('pelanggan.store')->middleware('role:SUPERUSER,MARKETING,TEKNISI');
    Route::get('/pelanggan/edit/{id}', [PelangganController::class, 'edit'])->name('pelanggan.edit')->middleware('role:SUPERUSER,MARKETING,TEKNISI');
    ;
    Route::post('/pelanggan/update/{id}', [PelangganController::class, 'update'])->name('pelanggan.update')->middleware('role:SUPERUSER,MARKETING,TEKNISI');
    Route::post('/pelanggan/delete', [PelangganController::class, 'delete'])->name('pelanggan.delete')->middleware('role:SUPERUSER,MARKETING,TEKNISI');

    Route::get('/pemasangan', [PemasanganController::class, 'index'])->name('pemasangan')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::get('/pemasangan/create', [PemasanganController::class, 'create'])->name('pemasangan.create')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::post('/pemasangan/store', [PemasanganController::class, 'store'])->name('pemasangan.store')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::get('/pemasangan/edit/{id}', [PemasanganController::class, 'edit'])->name('pemasangan.edit')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::post('/pemasangan/update/{id}', [PemasanganController::class, 'update'])->name('pemasangan.update')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::delete('/pemasangan/destroy/{id}', [PemasanganController::class, 'destroy'])->name('pemasangan.destroy')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::post('/pemasangan/pick-order', [PemasanganController::class, 'pickOrder'])->name('pemasangan.pick_order');
    Route::patch('/pemasangan/complete/{id}', [PemasanganController::class, 'complete'])->name('pemasangan.complete');
    Route::get('/pemasangan/{id}/detail', [\App\Http\Controllers\PemasanganController::class, 'detail'])->name('pemasangan.detail');

    Route::get('/kurir-giveaway', [KurirController::class, 'index'])->name('kurir-giveaway')->middleware('role:SUPERUSER,TEKNISI,MARKETING');
    Route::post('/kurir-giveaway/pick-order/{id}', [KurirController::class, 'pickOrder'])->name('kurir-giveaway.pick-order');
    Route::post('/kurir-giveaway/update-foto/{id}', [KurirController::class, 'updateFoto'])->name('kurir.updateFoto');
    Route::get('/kurir-giveaway/check-photo/{id}', [KurirGiveawayController::class, 'checkPhoto']);
    Route::post('kurir-giveaway/complete/{id}', [KurirGiveawayController::class, 'complete'])->name('kurir-giveaway.complete');

    Route::middleware(['auth', 'role:SUPERUSER,TEKNISI,MARKETING'])->group(function () {
        Route::resource('giveaway', GiveAwayController::class);
    });

    // Definisi route untuk asset management
    Route::middleware(['auth', 'role:SUPERUSER,TEKNISI,MARKETING'])->group(function () {
        Route::get('/data-asset', [AssetController::class, 'index'])->name('data-asset.index');
        Route::get('/data-asset/create', [AssetController::class, 'create'])->name('data-asset.create');
        Route::post('/data-asset', [AssetController::class, 'store'])->name('data-asset.store');
        Route::get('/data-asset/{id}/edit', [AssetController::class, 'edit'])->name('data-asset.edit');
        Route::put('/data-asset/{id}', [AssetController::class, 'update'])->name('data-asset.update');
        Route::delete('/data-asset/{id}', [AssetController::class, 'destroy'])->name('data-asset.destroy');
    });

    Route::get('/support-ticket', [SupportTicketController::class, 'index'])->name('support_ticket.index');
    Route::get('/support-ticket/create', [SupportTicketController::class, 'create'])->name('support_ticket.create');
    Route::post('/support-ticket/store', [SupportTicketController::class, 'store'])->name('support_ticket.store');
    Route::post('/support-ticket/update-status/{id}', [SupportTicketController::class, 'updateStatus'])->name('support_ticket.updateStatus');
    Route::get('/support-ticket/search-customer', [SupportTicketController::class, 'searchCustomer'])->name('support_ticket.searchCustomer');

    Route::get('/resolve-ticket', [ResolveTicketController::class, 'index'])->name('resolve_ticket.index');
    Route::post('/resolve-ticket/solve/{id}', [ResolveTicketController::class, 'solve'])->name('resolve_ticket.solve');

    Route::post('/absensi/in', [AttendanceController::class, 'absenIn'])->name('absensi.in');
    Route::post('/absensi/out', [AttendanceController::class, 'absenOut'])->name('absensi.out');

    Route::get('/attendance-monitor', [AttendanceMonitorController::class, 'index'])->name('attendance_monitor.index');
    Route::get('/attendance-monitor/{id}', [AttendanceMonitorController::class, 'show'])->name('attendance_monitor.show');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
