<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Website Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Rentiva', 'group' => 'general', 'description' => 'Nama platform rental'],
            ['key' => 'site_tagline', 'value' => 'Sewa Kost & Kamar Praktis, Aman & Terpercaya', 'group' => 'general', 'description' => 'Tagline utama'],
            ['key' => 'contact_phone', 'value' => '+62 812-3456-7890', 'group' => 'contact', 'description' => 'Nomor kontak WhatsApp'],
            ['key' => 'contact_email', 'value' => 'halo@rentiva.id', 'group' => 'contact', 'description' => 'Email bantuan resmi'],
            ['key' => 'office_address', 'value' => 'Jl. Kaliurang KM 5 No. 88, Sleman, D.I. Yogyakarta', 'group' => 'contact', 'description' => 'Alamat kantor operasional'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/rentiva.id', 'group' => 'social', 'description' => 'Akun Instagram resmi'],
            ['key' => 'meta_default_description', 'value' => 'Platform marketplace kost dan properti rental modern terpercaya di Indonesia.', 'group' => 'seo', 'description' => 'Meta description default'],
        ];

        foreach ($settings as $s) {
            WebsiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 2. Homepage Modular Sections
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Temukan Kost Idaman Dekat Kampus & Kantor',
                'subtitle' => 'Jelajahi ribuan pilihan kost eksklusif, bulanan, dan tahunan dengan fasilitas lengkap dan proses booking transparan.',
                'order' => 1,
                'is_visible' => true,
            ],
            [
                'section_key' => 'campus_search',
                'title' => 'Cari Dekat Kampus Populer',
                'subtitle' => 'Temukan kost strategis dengan jarak tempuh jalan kaki ke kampus favoritmu.',
                'order' => 2,
                'is_visible' => true,
            ],
            [
                'section_key' => 'featured_properties',
                'title' => 'Kost Pilihan Rekomendasi',
                'subtitle' => 'Properti kost terverifikasi dengan fasilitas terbaik dan ulasan bintang tertinggi.',
                'order' => 3,
                'is_visible' => true,
            ],
            [
                'section_key' => 'stats',
                'title' => 'Kenapa Ribuan Penyewa Memilih Rentiva?',
                'subtitle' => 'Platform sewa modern yang menjamin kenyamanan tinggal dan kepastian ketersediaan kamar.',
                'order' => 4,
                'is_visible' => true,
            ],
            [
                'section_key' => 'testimonials',
                'title' => 'Cerita Bahagia Penghuni & Pemilik Kost',
                'subtitle' => 'Pengalaman nyata dari mereka yang telah merasakan kemudahan menyewa melalui Rentiva.',
                'order' => 5,
                'is_visible' => true,
            ],
            [
                'section_key' => 'faq',
                'title' => 'Pertanyaan yang Sering Diajukan (FAQ)',
                'subtitle' => 'Segala hal yang perlu Anda ketahui mengenai proses booking, pembayaran, dan sewa di Rentiva.',
                'order' => 6,
                'is_visible' => true,
            ],
            [
                'section_key' => 'articles',
                'title' => 'Artikel & Panduan Edukasi',
                'subtitle' => 'Tips mencari hunian, gaya hidup anak kost, dan panduan manajemen properti untuk pemilik.',
                'order' => 7,
                'is_visible' => true,
            ],
            [
                'section_key' => 'cta',
                'title' => 'Punya Properti atau Kamar Kost Kosong?',
                'subtitle' => 'Daftarkan properti Anda sekarang dan jangkau ribuan calon penyewa mahasiswa dan profesional.',
                'order' => 8,
                'is_visible' => true,
            ],
        ];

        foreach ($sections as $sec) {
            HomepageSection::updateOrCreate(['section_key' => $sec['section_key']], $sec);
        }

        // 3. Sample FAQs
        $faqs = [
            [
                'question' => 'Bagaimana cara mengajukan sewa kamar di Rentiva?',
                'answer' => 'Pilih properti dan tipe kamar yang diinginkan, klik "Pilih Kamar & Ajukan Sewa", tentukan tanggal mulai sewa serta durasi, lalu kirim pengajuan. Pemilik kost akan mengonfirmasi dalam waktu maksimal 24 jam.',
                'category' => 'booking',
                'order' => 1,
            ],
            [
                'question' => 'Apakah harga sewa di Rentiva sudah termasuk deposit jaminan?',
                'answer' => 'Rincian deposit jaminan (jika diwajibkan oleh pemilik kost) tercantum secara transparan pada rincian harga sebelum Anda mengajukan booking. Deposit akan dikembalikan saat masa sewa selesai sesuai syarat ketentuan.',
                'category' => 'payment',
                'order' => 2,
            ],
            [
                'question' => 'Bagaimana jika ada kerusakan fasilitas kamar selama masa tinggal?',
                'answer' => 'Penyewa dapat menggunakan fitur "Lapor Keluhan Kamar" di Portal Penyewa. Laporan akan langsung diteruskan ke pemilik kost untuk penjadwalan perbaikan oleh teknisi.',
                'category' => 'tenant',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana cara pemilik kost mendaftarkan properti?',
                'answer' => 'Masuk ke Portal Pemilik (Owner), pilih "Tambah Properti Baru", lengkapi detail kamar dan foto, lalu ajukan verifikasi. Tim Rentiva akan memverifikasi keaslian properti sebelum dipublikasikan ke publik.',
                'category' => 'owner',
                'order' => 4,
            ],
        ];

        foreach ($faqs as $f) {
            Faq::updateOrCreate(['question' => $f['question']], $f);
        }

        // 4. Sample Testimonials
        $testimonials = [
            [
                'name' => 'Dimas Prasetyo',
                'role' => 'Mahasiswa UGM (Penyewa)',
                'content' => 'Proses booking lewat Rentiva sangat cepat dan tanpa ribet. Kamar yang saya tempati persis seperti foto yang ada di listing. Sangat recommended!',
                'rating' => 5,
                'order' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'role' => 'Karyawan Swasta (Penyewa)',
                'content' => 'Fitur lapor keluhan fasilitas sangat membantu. Kemarin AC kamar sempat kurang dingin, lapor pagi siang harinya teknisi sudah datang memperbaiki.',
                'rating' => 5,
                'order' => 2,
            ],
            [
                'name' => 'Bambang Sudiro',
                'role' => 'Pemilik Kost Eksklusif Sleman',
                'content' => 'Dashboard pemilik sangat rapi. Saya bisa memantau tingkat okupansi dan menerima pembayaran sewa bulanan dengan terstruktur tanpa catatan manual.',
                'rating' => 5,
                'order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(['name' => $t['name']], $t);
        }

        // 5. Sample Educational Articles
        $superAdmin = User::first();
        $articles = [
            [
                'author_id' => $superAdmin?->id,
                'title' => '7 Tips Cerdas Memilih Kost Nyaman & Aman untuk Mahasiswa Baru',
                'slug' => '7-tips-cerdas-memilih-kost-nyaman-dan-aman',
                'excerpt' => 'Panduan lengkap bagi mahasiswa baru dalam menentukan lokasi kost, fasilitas penting, keamanan lingkungan, serta estimasi anggaran sewa.',
                'body' => "Menempuh pendidikan di perguruan tinggi merupakan babak baru yang menyenangkan. Salah satu hal terpenting yang perlu dipersiapkan adalah tempat tinggal atau kost.\n\nBerikut 7 tips utama:\n1. Tentukan Jarak & Aksesibilitas ke Kampus\n2. Cek Keamanan Lingkungan Sekitar\n3. Pastikan Sirkulasi Udara dan Pencahayaan Alami\n4. Tanyakan Rincian Biaya Tambahan (Listrik, Air, WiFi)\n5. Cek Kebijakan Jam Malam dan Tamu\n6. Survei Ketersediaan Fasilitas Bersama\n7. Manfaatkan Platform Terpercaya seperti Rentiva untuk Verifikasi Asli.",
                'category' => 'tips',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'meta_title' => '7 Tips Cerdas Memilih Kost Nyaman & Aman — Rentiva',
                'meta_description' => 'Panduan cerdas memilih kost nyaman, aman, dan hemat untuk mahasiswa baru dekat kampus.',
            ],
            [
                'author_id' => $superAdmin?->id,
                'title' => 'Strategi Meningkatkan Okupansi Kost Hingga 100% Sepanjang Tahun',
                'slug' => 'strategi-meningkatkan-okupansi-kost-hingga-100-persen',
                'excerpt' => 'Pelajari cara pemilik properti modern memaksimalkan tingkat keterisian kamar melalui kualitas layanan dan pemasaran digital.',
                'body' => "Bisnis kos-kosan kini semakin kompetitif. Pemilik kost yang sukses tidak hanya mengandalkan lokasi, tetapi juga pengalaman tinggal penyewa.\n\nStrategi utama meliputi:\n1. Foto Listing Berkualitas Tinggi dan Deskripsi Transparan\n2. Respons Cepat terhadap Permintaan Calon Penyewa\n3. Pemeliharaan Fasilitas yang Teratur\n4. Skema Tarif Fleksibel (Bulanan, Semester, Tahunan)\n5. Sistem Manajemen Digital Tanpa Ribet.",
                'category' => 'guide',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'meta_title' => 'Strategi Meningkatkan Okupansi Kost — Rentiva',
                'meta_description' => 'Tips dan trik bagi pemilik kost untuk mempertahankan tingkat hunian kamar maksimal.',
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 6. Navigation Menus
        $headerMenu = Menu::updateOrCreate(['location' => 'header'], ['name' => 'Menu Utama Header']);
        MenuItem::updateOrCreate(['menu_id' => $headerMenu->id, 'title' => 'Jelajahi Kost'], ['url' => '/properties', 'order' => 1, 'is_active' => true]);
        MenuItem::updateOrCreate(['menu_id' => $headerMenu->id, 'title' => 'Artikel & Tips'], ['url' => '/articles', 'order' => 2, 'is_active' => true]);
        MenuItem::updateOrCreate(['menu_id' => $headerMenu->id, 'title' => 'Bantuan & FAQ'], ['url' => '/#faq', 'order' => 3, 'is_active' => true]);
    }
}
