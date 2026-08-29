<?php

namespace App\Services;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Article;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\Menu;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CmsService
{
    public function setting(string $key, mixed $default = null): mixed
    {
        return WebsiteSetting::get($key, $default);
    }

    public function menu(string $location): ?Menu
    {
        return Cache::remember('cms_menu_' . $location, 3600, function () use ($location) {
            return Menu::where('location', $location)
                ->with('activeItems')
                ->first();
        });
    }

    public function homepageSections(): Collection
    {
        return Cache::remember('cms_homepage_sections', 3600, function () {
            return HomepageSection::visible()->get();
        });
    }

    public function featuredProperties(int $limit = 6): Collection
    {
        return Cache::remember('cms_featured_properties_' . $limit, 1800, function () use ($limit) {
            $featured = Property::where('status', PropertyStatus::PUBLISHED)
                ->where('verification_status', VerificationStatus::VERIFIED)
                ->where('featured', true)
                ->with(['coverImage', 'location', 'propertyType', 'units.pricePlans'])
                ->limit($limit)
                ->get();

            if ($featured->count() < $limit) {
                $additional = Property::where('status', PropertyStatus::PUBLISHED)
                    ->where('verification_status', VerificationStatus::VERIFIED)
                    ->whereNotIn('id', $featured->pluck('id'))
                    ->with(['coverImage', 'location', 'propertyType', 'units.pricePlans'])
                    ->latest('published_at')
                    ->limit($limit - $featured->count())
                    ->get();

                $featured = $featured->concat($additional);
            }

            return $featured;
        });
    }

    public function testimonials(int $limit = 6): Collection
    {
        return Cache::remember('cms_testimonials_' . $limit, 3600, function () use ($limit) {
            return Testimonial::active()->limit($limit)->get();
        });
    }

    public function faqs(?string $category = null): Collection
    {
        $cacheKey = 'cms_faqs_' . ($category ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($category) {
            $query = Faq::active();
            if ($category) {
                $query->where('category', $category);
            }

            return $query->get();
        });
    }

    public function latestArticles(int $limit = 3): Collection
    {
        return Cache::remember('cms_latest_articles_' . $limit, 1800, function () use ($limit) {
            return Article::published()
                ->with('author')
                ->limit($limit)
                ->get();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('website_settings_all');
        Cache::forget('cms_homepage_sections');
        Cache::forget('cms_testimonials_6');
        Cache::forget('cms_faqs_all');
        Cache::forget('cms_latest_articles_3');
        Cache::forget('cms_featured_properties_6');
    }
}
