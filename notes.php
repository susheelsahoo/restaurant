CREATE TABLE customers (
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NULL,
email VARCHAR(150) NOT NULL UNIQUE,
phone VARCHAR(20) NULL,
date_of_birth DATE NULL,
date_of_anniversary DATE NULL,
gender ENUM('male','female','other') NULL,
address_line1 VARCHAR(255) NULL,
address_line2 VARCHAR(255) NULL,
city VARCHAR(100) NULL,
state VARCHAR(100) NULL,
postal_code VARCHAR(20) NULL,
country VARCHAR(100) NULL,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

INDEX idx_email (email),
INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE reservations
ADD customer_id BIGINT UNSIGNED NULL AFTER id;

ALTER TABLE reservations
ADD CONSTRAINT fk_reservation_customer
FOREIGN KEY (customer_id) REFERENCES customers(id)
ON DELETE SET NULL;

ALTER TABLE `reservations` CHANGE `customer_name` `customer_name` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `phone` `phone` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `email` `email` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;


ALTER TABLE `gallery_images` CHANGE `frontend_display` `home_display` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `gallery_images` ADD `gallery_display` TINYINT(1) NOT NULL DEFAULT '1' AFTER `home_display`;


Great! You're looking to build a **complete blog module in Laravel** with:

* ✅ **CRUD for blog posts**
* 🏷️ **Tags**
* 📂 **Categories**
* 🔍 **SEO support** (meta title, meta description)
* 🖼️ **Image upload**
* 🔒 Admin-only management

---

## 🏗️ Here's the Complete Blog Module Architecture

We'll build:

### 📦 Models

* `Blog`
* `Category`
* `Tag`

### 🗃️ Relationships

* Blog ➝ belongsTo Category
* Blog ➝ belongsToMany Tags

---

## ✅ Step-by-Step Laravel Blog Module with Categories, Tags, and SEO

### 🔹 Step 1: Create Models + Migrations + Controllers

```bash
php artisan make:model Blog -mcr
php artisan make:model Category -mcr
php artisan make:model Tag -mcr
```

Also create a pivot table:

```bash
php artisan make:migration create_blog_tag_table
```

---

### 🔹 Step 2: Migrations

#### 📝 `blogs` table:

```php
Schema::create('blogs', function (Blueprint $table) {
$table->id();
$table->string('title');
$table->string('slug')->unique();
$table->foreignId('category_id')->constrained()->onDelete('cascade');
$table->text('content');
$table->string('image')->nullable();
$table->enum('status', ['draft', 'published'])->default('draft');
$table->string('meta_title')->nullable();
$table->string('meta_description')->nullable();
$table->timestamps();
});
```

#### 📁 `categories` table:

```php
Schema::create('categories', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->string('slug')->unique();
$table->timestamps();
});
```

#### 🏷️ `tags` table:

```php
Schema::create('tags', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->string('slug')->unique();
$table->timestamps();
});
```

#### 🔗 `blog_tag` pivot table:

```php
Schema::create('blog_tag', function (Blueprint $table) {
$table->id();
$table->foreignId('blog_id')->constrained()->onDelete('cascade');
$table->foreignId('tag_id')->constrained()->onDelete('cascade');
});
```

Run all migrations:

```bash
php artisan migrate
```

---

### 🔹 Step 3: Define Relationships

#### `Blog.php`

```php
public function category()
{
return $this->belongsTo(Category::class);
}

public function tags()
{
return $this->belongsToMany(Tag::class);
}
```

#### `Category.php`

```php
public function blogs()
{
return $this->hasMany(Blog::class);
}
```

#### `Tag.php`

```php
public function blogs()
{
return $this->belongsToMany(Blog::class);
}
```

---

### 🔹 Step 4: Routes (`web.php`)

```php
Route::middleware('auth')->prefix('admin')->group(function () {
Route::resource('blogs', BlogController::class);
Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);
});

Route::get('/blog/{slug}', [BlogController::class, 'showPublic'])->name('blog.show');
```

---

### 🔹 Step 5: Controller Logic (BlogController)

#### `store()` (simplified):

```php
public function store(Request $request)
{
$request->validate([
'title' => 'required',
'slug' => 'required|unique:blogs',
'content' => 'required',
'category_id' => 'required|exists:categories,id',
'tags' => 'nullable|array',
'tags.*' => 'exists:tags,id',
'image' => 'nullable|image|max:2048',
]);

$data = $request->only(['title', 'slug', 'content', 'category_id', 'meta_title', 'meta_description', 'status']);

if ($request->hasFile('image')) {
$data['image'] = $request->file('image')->store('blogs', 'public');
}

$blog = Blog::create($data);
$blog->tags()->sync($request->tags);

return redirect()->route('blogs.index')->with('success', 'Blog created successfully.');
}
```

Also implement `update()`, `destroy()`, `index()`, and `showPublic()` similarly.

---

### 🔹 Step 6: Blade Views

Create Blade files under:

```
resources/views/admin/blogs/
resources/views/admin/categories/
resources/views/admin/tags/
resources/views/blog/
```

In blog create/edit form:

```blade
<form method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Title -->
    <input type="text" name="title">

    <!-- Slug -->
    <input type="text" name="slug">

    <!-- Category -->
    <select name="category_id">
        @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>

    <!-- Tags -->
    <select name="tags[]" multiple>
        @foreach($tags as $tag)
        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>

    <!-- Content -->
    <textarea name="content"></textarea>

    <!-- Meta Title / Description -->
    <input type="text" name="meta_title">
    <input type="text" name="meta_description">

    <!-- Image -->
    <input type="file" name="image">

    <!-- Status -->
    <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
    </select>

    <button type="submit">Save</button>
</form>
```

---

### 🔹 Step 7: SEO in Blog View

```blade
<!-- blog/show.blade.php -->
@section('title', $blog->meta_title ?? $blog->title)
@section('description', $blog->meta_description)

<h1>{{ $blog->title }}</h1>
<p>{{ $blog->content }}</p>

@foreach($blog->tags as $tag)
<span>#{{ $tag->name }}</span>
@endforeach
```

---

## 🚀 Optional Enhancements

* Add a WYSIWYG editor like [Trix](https://github.com/basecamp/trix) or [TinyMCE](https://www.tiny.cloud/)
* Add image thumbnails
* Paginate blog list
* Use `Spatie\Sluggable` for auto slugging
* Use `Spatie\Tags` if you want tag magic

---

## ✅ Want Me to Generate the Full Code?

If you want, I can:

* Scaffold full Blade templates
* Generate controller/store/update logic
* Auto-generate Slug
* Add Trix Editor or CKEditor
* Add real SEO meta tags and OpenGraph tags

👉 Just say: **"Generate the full blog module files"**, and I’ll drop all the code.