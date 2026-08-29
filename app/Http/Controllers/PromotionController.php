<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Services\SeoService;
use App\Support\SeoData;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::active()
            ->latest()
            ->get();

        $seo = new SeoData(
            title: 'Kupon Promo & Diskon Sewa Kost Terbaru — Rentiva',
            description: 'Dapatkan berbagai penawaran voucher promo diskon sewa kamar kost, cashback, dan potongan harga eksklusif di Rentiva.',
            canonical: route('promotions.index')
        );

        return view('promotions.index', compact('promotions', 'seo'));
    }
}
