<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';
    protected $description = 'Generate sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url('/'))->setPriority(1.0));

        Page::query()
            ->where('is_deleted', false)
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['slug', 'updated_at'])
            ->each(function (Page $page) use ($sitemap) {
                // Home page is already included as "/"
                if ($page->slug === 'home') {
                    return;
                }

                $sitemap->add(
                    Url::create(url('/' . ltrim($page->slug, '/')))
                        ->setLastModificationDate($page->updated_at)
                );
            });

        $sitemap->add(Url::create(url('/blogs')));

        $blogQuery = Blog::query()
            ->where('is_deleted', false)
            ->whereNotNull('slug')
            ->where('slug', '!=', '');

        if (Schema::hasColumn('blogs', 'status')) {
            $blogQuery->where('status', 'published');
        } elseif (Schema::hasColumn('blogs', 'is_published')) {
            $blogQuery->where('is_published', true);
        }

        $blogQuery
            ->get(['slug', 'updated_at'])
            ->each(function (Blog $blog) use ($sitemap) {
                $sitemap->add(
                    Url::create(url('/blog/' . ltrim($blog->slug, '/')))
                        ->setLastModificationDate($blog->updated_at)
                );
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
