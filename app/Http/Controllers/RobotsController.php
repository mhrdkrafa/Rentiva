<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /tenant/\n";
        $content .= "Disallow: /owner/\n";
        $content .= "Disallow: /messages/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Allow: /\n";
        $content .= "Allow: /properties/\n";
        $content .= "Allow: /articles/\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
