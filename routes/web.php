<?php

use App\Http\Controllers\Apps\PermissionManagementController;
use App\Http\Controllers\Apps\RoleManagementController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// ========== PUBLIC ROUTES ==========
Route::get('/', [FrontendPageController::class, 'index'])->name('home');
// Route::get('/about', [FrontendPageController::class, 'index'])->name('about');

Route::get('/blogs', [FrontendPageController::class, 'blogs'])->name('blog.index');
Route::get('/blog/{slug}', [FrontendPageController::class, 'showBlog'])->name('blog.show');

// Page Slug (must be last, and exclude reserved keywords like admin, blog, auth, storage, etc.)
Route::get('/{slug}', [FrontendPageController::class, 'index'])
    ->where('slug', '^(?!blog|email|admin|auth|storage|error).*$')
    ->name('page');

// Social Login
Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

// Error Page
Route::get('/error', function () {
    abort(500);
});


Route::post('/leads', [LeadsController::class, 'store'])->name('leads.store');

Route::get('/email', function () {
    try {
        Mail::raw('This is a test email from Laravel SMTP setup.', function ($message) {
            $message->to('susheelcs24@gmail.com') // 👈 Replace with your email
                ->subject('Test Email from Laravel');
        });

        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Failed to send email. Error: ' . $e->getMessage();
    }
});

// ========== ADMIN ROUTES ==========
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/pages', PageController::class)->names('pages');
    Route::resource('/gallery', GalleryImageController::class)->names('gallery');
    Route::resource('/banners', BannerController::class)->names('banners');
    Route::resource('/contacts', ContactController::class)->names('contacts');
    Route::resource('/leads', LeadsController::class)->names('leads');
    Route::resource('/settings', SettingController::class)->names('settings');

    Route::name('user-management.')->group(function () {
        Route::resource('/user-management/users', UserManagementController::class);
        Route::resource('/user-management/roles', RoleManagementController::class);
        Route::resource('/user-management/permissions', PermissionManagementController::class);
    });

    Route::resource('/blogs', BlogController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/tags', TagController::class);
});


require __DIR__ . '/auth.php';
