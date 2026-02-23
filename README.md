# Tifliso

## Sitemap Generation

This project includes a custom Artisan command to generate `public/sitemap.xml`.

### Command

```bash
php artisan generate:sitemap
```

### What is included in sitemap

- Home page: `/`
- Active CMS pages from `pages` table (`is_active = 1`)
  - URL format: `/{slug}`
  - `home` slug is skipped (already covered by `/`)
- Blog listing page: `/blogs`
- Published blog details from `blogs` table
  - URL format: `/blog/{slug}`
  - Supports either:
    - `status = 'published'`, or
    - `is_published = 1`

### Scheduler (Cron)

Sitemap generation is scheduled in `app/Console/Kernel.php`:

```php
$schedule->command('generate:sitemap')->weeklyOn(1, '02:00');
```

This runs every Monday at 02:00.

To make Laravel scheduler work, ensure server cron runs:

```bash
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```
