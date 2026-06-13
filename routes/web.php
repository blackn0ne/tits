<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Site\SiteSeoController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\UtilityPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('robots.txt', [SiteSeoController::class, 'robots'])->name('seo.robots');
Route::get('sitemap.xml', [SiteSeoController::class, 'sitemap'])->name('seo.sitemap');

Route::get('maintenance', [UtilityPageController::class, 'maintenance'])
    ->name('maintenance');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::match(['get', 'post'], 'locale/{locale}', [LocaleController::class, 'update'])
    ->name('locale.update');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/auth.php';

Route::fallback([UtilityPageController::class, 'notFound'])
    ->name('fallback');
