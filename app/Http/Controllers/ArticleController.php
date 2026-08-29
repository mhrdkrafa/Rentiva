<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\SeoService;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::published()->with('author');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('excerpt', 'like', $keyword)
                    ->orWhere('body', 'like', $keyword);
            });
        }

        $articles = $query->paginate(9)->withQueryString();
        $categories = [
            'tips' => 'Tips & Trik Kost',
            'guide' => 'Panduan Manajemen',
            'lifestyle' => 'Gaya Hidup',
            'news' => 'Berita & Pengumuman',
        ];

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show(string $slug): View
    {
        $article = Article::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        $seo = SeoService::article($article);

        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles', 'seo'));
    }
}
