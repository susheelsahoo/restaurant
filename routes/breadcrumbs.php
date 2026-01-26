<?php

use App\Models\User;
use App\Models\Page;
use App\Models\GalleryImage;
use App\Models\Banner;
use App\Models\Lead;
use App\Models\ContactMessage;
use App\Models\SeoSetting;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Role;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;



Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('admin.dashboard'));
});

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('admin.dashboard'));
});

Breadcrumbs::for('admin.pages.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Pages', route('admin.pages.index'));
});

Breadcrumbs::for('pages.create', function (BreadcrumbTrail $trail) {
    $trail->parent('pages.index');
    $trail->push('Create Page', route('admin.pages.create'));
});

Breadcrumbs::for('pages.edit', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('pages.index');
    $trail->push('Edit: ' . $page->title, route('admin.pages.edit', $page));
});

Breadcrumbs::for('admin.banners.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Banners', route('admin.banners.index'));
});

Breadcrumbs::for('banners.create', function (BreadcrumbTrail $trail) {
    $trail->parent('banners.index');
    $trail->push('Create Banner', route('admin.banners.create'));
});

Breadcrumbs::for('banners.edit', function (BreadcrumbTrail $trail, Banner $banner) {
    $trail->parent('banners.index');
    $trail->push('Edit Banner', route('admin.banners.edit', $banner));
});


// Dashboard > Gallery
Breadcrumbs::for('admin.gallery.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Gallery', route('admin.gallery.index'));
});

// Dashboard > Gallery > Upload
Breadcrumbs::for('admin.gallery.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.gallery.index');
    $trail->push('Upload Image', route('admin.gallery.create'));
});

Breadcrumbs::for('admin.contacts.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Contact', route('admin.contacts.index'));
});



Breadcrumbs::for('contact-messages.show', function (BreadcrumbTrail $trail, ContactMessage $message) {
    $trail->parent('contact-messages.index');
    $trail->push('Message from ' . $message->name, route('admin.contact-messages.show', $message));
});


Breadcrumbs::for('admin.leads.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Leads', route('admin.leads.index'));
});

Breadcrumbs::for('admin.leads.show', function (BreadcrumbTrail $trail, Lead $lead) {
    $trail->parent('admin.leads.index');
    $trail->push('Lead: ' . $lead->name, route('admin.leads.show', $lead));
});


Breadcrumbs::for('seo-settings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('SEO Settings', route('admin.seo-settings.index'));
});


Breadcrumbs::for('seo-settings.edit', function (BreadcrumbTrail $trail, SeoSetting $seoSetting) {
    $trail->parent('seo-settings.index');
    $trail->push('Edit SEO for ' . ucfirst($seoSetting->page), route('admin.seo-settings.edit', $seoSetting));
});






Breadcrumbs::for('user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Management', route('admin.user-management.users.index'));
});


Breadcrumbs::for('user-management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Users', route('admin.user-management.users.index'));
});


Breadcrumbs::for('user-management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user-management.users.index');
    $trail->push(ucwords($user->name), route('admin.user-management.users.show', $user));
});


Breadcrumbs::for('user-management.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Roles', route('admin.user-management.roles.index'));
});


Breadcrumbs::for('user-management.roles.show', function (BreadcrumbTrail $trail, Role $role) {
    $trail->parent('user-management.roles.index');
    $trail->push(ucwords($role->name), route('admin.user-management.roles.show', $role));
});


Breadcrumbs::for('user-management.permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Permissions', route('admin.user-management.permissions.index'));
});

Breadcrumbs::for('admin.blogs.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Blogs', route('admin.blogs.index'));
});

Breadcrumbs::for('admin.blogs.create', function ($trail) {
    $trail->parent('blogs.index');
    $trail->push('Create', route('admin.blogs.create'));
});

Breadcrumbs::for('admin.blogs.edit', function ($trail, Blog $blog) {
    $trail->parent('blogs.index');
    $trail->push('Edit: ' . Str::limit($blog->title, 30), route('admin.blogs.edit', $blog));
});

Breadcrumbs::for('admin.categories.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Categories', route('admin.categories.index'));
});

Breadcrumbs::for('admin.categories.create', function ($trail) {
    $trail->parent('categories.index');
    $trail->push('Create', route('admin.categories.create'));
});

Breadcrumbs::for('admin.categories.edit', function ($trail, Category $category) {
    $trail->parent('categories.index');
    $trail->push('Edit: ' . Str::limit($category->name, 30), route('admin.categories.edit', $category));
});

Breadcrumbs::for('admin.tags.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Tags', route('admin.tags.index'));
});

Breadcrumbs::for('admin.tags.create', function ($trail) {
    $trail->parent('tags.index');
    $trail->push('Create', route('admin.tags.create'));
});

Breadcrumbs::for('admin.tags.edit', function ($trail, Tag $tag) {
    $trail->parent('tags.index');
    $trail->push('Edit: ' . $tag->name, route('admin.tags.edit', $tag));
});
