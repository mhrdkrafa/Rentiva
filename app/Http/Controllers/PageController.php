<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Support\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function faq(): View
    {
        $faqs = Faq::active()->get();
        $seo = new SeoData(
            title: 'Tanya Jawab & Bantuan (FAQ) — Rentiva',
            description: 'Temukan jawaban atas pertanyaan seputar sewa kamar, verifikasi properti, pembayaran aman, dan tata tertib hunian di Rentiva.'
        );

        return view('pages.faq', compact('faqs', 'seo'));
    }

    public function terms(): View
    {
        $seo = new SeoData(
            title: 'Syarat & Ketentuan Pengguna — Rentiva',
            description: 'Ketentuan hukum, hak, dan kewajiban penyewa dan mitra pemilik kost di platform Rentiva.'
        );

        return view('pages.terms', compact('seo'));
    }

    public function privacy(): View
    {
        $seo = new SeoData(
            title: 'Kebijakan Privasi & Keamanan Data — Rentiva',
            description: 'Bagaimana Rentiva melindungi informasi pribadi, dokumen identitas, dan data keuangan Anda.'
        );

        return view('pages.privacy', compact('seo'));
    }

    public function contact(): View
    {
        $seo = new SeoData(
            title: 'Hubungi Tim Bantuan Rentiva',
            description: 'Layanan pelanggan 24/7 dan pusat bantuan mitra pemilik serta penyewa kost Rentiva.'
        );

        return view('pages.contact', compact('seo'));
    }
}
