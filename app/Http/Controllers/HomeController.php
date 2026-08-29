<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PropertyType;
use App\Services\CmsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(CmsService $cmsService): View
    {
        $sections = $cmsService->homepageSections();
        $featuredProperties = $cmsService->featuredProperties(6);
        $testimonials = $cmsService->testimonials(6);
        $faqs = $cmsService->faqs();
        $latestArticles = $cmsService->latestArticles(3);

        $popularLocations = Location::where('is_active', true)->withCount('properties')->orderByDesc('properties_count')->limit(6)->get();
        $propertyTypes = PropertyType::where('is_active', true)->limit(6)->get();

        return view('welcome', compact(
            'sections',
            'featuredProperties',
            'testimonials',
            'faqs',
            'latestArticles',
            'popularLocations',
            'propertyTypes'
        ));
    }
}
