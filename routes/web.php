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
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CustomerNoteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\WineCategoryController;
use App\Http\Controllers\WinesController;
use App\Http\Controllers\EmailTemplateController;
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

Route::get('/home-gallery', [FrontendPageController::class, 'homePageGallery'])->name('home.gallery');
Route::get('/home-blog', [FrontendPageController::class, 'homePageBlog'])->name('home.latest.blogs');
Route::get('/gallery.image', [FrontendPageController::class, 'homePageGallery'])->name('gallery.images');
Route::get('/blogs', [FrontendPageController::class, 'blogs'])->name('blog.index');
Route::get('/blogs/category/{slug}', [FrontendPageController::class, 'blogsByCategory'])->name('blog.category');
Route::get('/blog/{slug}', [FrontendPageController::class, 'showBlog'])->name('blog.show');

Route::get('/menu', [FrontendPageController::class, 'menu'])->name('menu.index');
Route::get('/menu/category/{slug}', [FrontendPageController::class, 'menuByCategory'])->name('menu.category');
Route::get('/menu/{slug}', [FrontendPageController::class, 'showMenu'])->name('menu.show');

Route::get('/wines', [FrontendPageController::class, 'wines'])->name('wines.index');
Route::get('/wines/category/{slug}', [FrontendPageController::class, 'winesByCategory'])->name('wines.category');
Route::get('/wines/{slug}', [FrontendPageController::class, 'showWine'])->name('wines.show');

Route::get('/customers/unsubscribe/{customer}', [CustomersController::class, 'unsubscribe'])
    ->name('customers.unsubscribe');

// Page Slug (must be last, and exclude reserved keywords like admin, blog, auth, storage, etc.)
Route::get('/{slug}', [FrontendPageController::class, 'index'])
    ->where('slug', '^(?!blog|menu|wines|email|admin|auth|storage|error).*$')
    ->name('page');

// Social Login
Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

// Error Page
Route::get('/error', function () {
    abort(500);
});

Route::post('/contact-us', [ContactController::class, 'store'])
    ->name('contact.store');

Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/reserve-table', [ReservationController::class, 'create'])
    ->name('reserve-table.form');
Route::post('/reserve-table', [ReservationController::class, 'store'])
    ->name('reserve-table.store');
Route::get('/slots/{date}', [ReservationController::class, 'slots']);



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
    Route::post('/gallery/toggle', [GalleryImageController::class, 'toggle'])->name('gallery.toggle');

    Route::resource('/customers', CustomersController::class)->names('customers');
    Route::post('/customers/notifications/send', [CustomersController::class, 'sendNotification'])->name('customers.notifications.send');
    Route::get('customer-notes/{customer}', [CustomerNoteController::class, 'show'])->name('admin.customer-notes.show');
    Route::get('customer-notes/create/{customer_id}', [CustomerNoteController::class, 'create'])->name('customer-notes.create');
    Route::post('customer-notes', [CustomerNoteController::class, 'store'])->name('customer-notes.store');
    Route::delete('customer-notes/{note}', [CustomerNoteController::class, 'destroy'])->name('customer-notes.destroy');

    Route::resource('/banners', BannerController::class)->names('banners');
    Route::resource('/contacts', ContactController::class)->names('contacts');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::resource('/bookings', BookingController::class)->names('bookings');
    Route::resource('/settings', SettingController::class)->names('settings');
    Route::resource('/email-templates', EmailTemplateController::class)->names('email-templates');

    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/dashboard', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Dashboard',
                'description' => 'This is the purchase order dashboard area.',
            ]);
        })->name('dashboard');
        Route::get('/requests', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Requests',
                'description' => 'Use this page for purchase request management.',
            ]);
        })->name('requests');
        Route::get('/approvals', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Approvals',
                'description' => 'Use this page for purchase approval workflows.',
            ]);
        })->name('approvals');
        Route::get('/products', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Products',
                'description' => 'Use this page to manage products linked to purchase orders.',
            ]);
        })->name('products');
        Route::get('/suppliers', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Suppliers',
                'description' => 'Use this page to manage supplier records.',
            ]);
        })->name('suppliers');
        Route::get('/deliveries', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Deliveries',
                'description' => 'Use this page to track purchase order deliveries.',
            ]);
        })->name('deliveries');
        Route::get('/reports', function () {
            return view('admin.placeholders.index', [
                'title' => 'PO Reports',
                'description' => 'Use this page for purchase order reporting.',
            ]);
        })->name('reports');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
    });

    Route::name('user-management.')->group(function () {
        Route::resource('/user-management/users', UserManagementController::class);
        Route::resource('/user-management/roles', RoleManagementController::class);
        Route::resource('/user-management/permissions', PermissionManagementController::class);
    });

    Route::resource('/blogs', BlogController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/tags', TagController::class);
    Route::resource('/menu-categories', MenuCategoryController::class);
    Route::resource('/menus', MenuController::class);
    Route::resource('/wine-categories', WineCategoryController::class);
    Route::resource('/wines', WinesController::class);
});


require __DIR__ . '/auth.php';
