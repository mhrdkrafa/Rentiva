<?php

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Article;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Services\CmsService;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
    $this->tenant = User::factory()->tenant()->create();
});

test('website settings can be retrieved and updated with fallback defaults', function () {
    $cmsService = new CmsService();

    // Default fallback
    expect($cmsService->setting('non_existing_key', 'Default Value'))->toBe('Default Value');

    // Set setting in database
    WebsiteSetting::set('site_title', 'Rentiva Marketplace', 'general');

    expect(WebsiteSetting::get('site_title'))->toBe('Rentiva Marketplace');
});

test('homepage renders dynamic modular sections from cms', function () {
    HomepageSection::create([
        'section_key' => 'hero',
        'title' => 'Temukan Kost Idaman Dekat Kampus',
        'subtitle' => 'Jelajahi ribuan pilihan kost eksklusif',
        'order' => 1,
        'is_visible' => true,
    ]);

    Faq::create([
        'question' => 'Apakah sewa kost di Rentiva aman?',
        'answer' => 'Sangat aman karena semua properti telah melalui proses verifikasi lapangan.',
        'category' => 'general',
        'order' => 1,
        'is_active' => true,
    ]);

    Testimonial::create([
        'name' => 'Budi Santoso',
        'role' => 'Mahasiswa UGM',
        'content' => 'Kost di Sagan sangat nyaman dan dekat kampus!',
        'rating' => 5,
        'is_active' => true,
    ]);

    $response = $this->get(route('home'));
    $response->assertOk()
        ->assertSee('Temukan Hunian Nyaman')
        ->assertSee('Apakah sewa kost di Rentiva aman?')
        ->assertSee('Budi Santoso')
        ->assertSee('Kost di Sagan sangat nyaman');
});

test('articles hub lists published articles and allows category filtering', function () {
    $articleTips = Article::create([
        'author_id' => $this->admin->id,
        'title' => '5 Tips Memilih Kost Mahasiswa Baru',
        'slug' => '5-tips-memilih-kost-mahasiswa-baru',
        'excerpt' => 'Panduan lengkap bagi mahasiswa baru mencari hunian.',
        'body' => 'Isi artikel lengkap tips memilih kost yang tepat dan aman.',
        'category' => 'tips',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $articleGuide = Article::create([
        'author_id' => $this->admin->id,
        'title' => 'Panduan Menjadi Pemilik Kost Sukses',
        'slug' => 'panduan-menjadi-pemilik-kost-sukses',
        'excerpt' => 'Strategi mengelola kamar kos dan keuangan.',
        'body' => 'Isi panduan bagi pemilik kost modern.',
        'category' => 'guide',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    // All published articles
    $response = $this->get(route('articles.index'));
    $response->assertOk()
        ->assertSee('5 Tips Memilih Kost Mahasiswa Baru')
        ->assertSee('Panduan Menjadi Pemilik Kost Sukses');

    // Filtered by category
    $responseTips = $this->get(route('articles.index', ['category' => 'tips']));
    $responseTips->assertOk()
        ->assertSee('5 Tips Memilih Kost Mahasiswa Baru')
        ->assertDontSee('Panduan Menjadi Pemilik Kost Sukses');
});

test('single article route displays article details and draft articles are hidden', function () {
    $publishedArticle = Article::create([
        'author_id' => $this->admin->id,
        'title' => 'Cara Menjaga Kebersihan Kamar Kost',
        'slug' => 'cara-menjaga-kebersihan-kamar-kost',
        'excerpt' => 'Tips harian menjaga kamar tetap rapi.',
        'body' => 'Menjaga kebersihan kamar kost sangat penting untuk kesehatan.',
        'category' => 'tips',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $draftArticle = Article::create([
        'author_id' => $this->admin->id,
        'title' => 'Draft Artikel Rahasia',
        'slug' => 'draft-artikel-rahasia',
        'excerpt' => 'Belum siap dipublikasikan.',
        'body' => 'Konten draft.',
        'category' => 'news',
        'is_published' => false,
    ]);

    // Published article renders 200
    $this->get(route('articles.show', $publishedArticle->slug))
        ->assertOk()
        ->assertSee('Cara Menjaga Kebersihan Kamar Kost')
        ->assertSee('Menjaga kebersihan kamar kost sangat penting');

    // Draft article returns 404
    $this->get(route('articles.show', $draftArticle->slug))
        ->assertNotFound();
});

test('faq and testimonial active scopes filter out inactive records', function () {
    $activeFaq = Faq::create([
        'question' => 'Faq Aktif',
        'answer' => 'Jawaban Faq Aktif',
        'is_active' => true,
    ]);

    $inactiveFaq = Faq::create([
        'question' => 'Faq Nonaktif',
        'answer' => 'Jawaban Faq Nonaktif',
        'is_active' => false,
    ]);

    expect(Faq::active()->pluck('id'))->toContain($activeFaq->id)
        ->and(Faq::active()->pluck('id'))->not->toContain($inactiveFaq->id);

    $activeTestimonial = Testimonial::create([
        'name' => 'User Aktif',
        'role' => 'Penyewa',
        'content' => 'Review bagus',
        'is_active' => true,
    ]);

    $inactiveTestimonial = Testimonial::create([
        'name' => 'User Nonaktif',
        'role' => 'Penyewa',
        'content' => 'Review dihapus',
        'is_active' => false,
    ]);

    expect(Testimonial::active()->pluck('id'))->toContain($activeTestimonial->id)
        ->and(Testimonial::active()->pluck('id'))->not->toContain($inactiveTestimonial->id);
});
