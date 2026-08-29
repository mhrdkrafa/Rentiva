<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Article;
use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $properties = Property::where('status', PropertyStatus::PUBLISHED)
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        $articles = Article::published()
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // 1. Homepage
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // 2. Catalog & Search
        $xml .= '<url>';
        $xml .= '<loc>' . route('properties.index') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // 3. Articles Hub
        $xml .= '<url>';
        $xml .= '<loc>' . route('articles.index') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';

        // 4. Properties
        foreach ($properties as $prop) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('properties.show', $prop->slug) . '</loc>';
            $xml .= '<lastmod>' . $prop->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        // 5. Articles
        foreach ($articles as $art) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('articles.show', $art->slug) . '</loc>';
            $xml .= '<lastmod>' . $art->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
