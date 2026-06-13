<?php

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\Sales\SalesClientController;
use App\Http\Controllers\Admin\Sales\SalesOrderController;
use App\Http\Controllers\Admin\Sales\SalesProductController;
use App\Http\Controllers\Admin\Sales\SalesReportController;
use App\Http\Controllers\Admin\Sales\SalesServiceController;
use App\Http\Controllers\Admin\SiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/site-settings')->name('dashboard');

    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('site-settings', [SiteSettingsController::class, 'edit'])->name('site-settings.edit');
    Route::post('site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');

    Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);
    Route::resource('blog-posts', BlogPostController::class)->except(['show']);
    Route::resource('project-categories', ProjectCategoryController::class)->except(['show']);
    Route::resource('projects', ProjectController::class)->except(['show']);

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::resource('products', SalesProductController::class)->except(['show']);
        Route::resource('services', SalesServiceController::class)->except(['show']);
        Route::resource('clients', SalesClientController::class)->except(['show']);
        Route::resource('orders', SalesOrderController::class)->except(['show']);
        Route::get('reports', [SalesReportController::class, 'index'])->name('reports.index');
    });
});
