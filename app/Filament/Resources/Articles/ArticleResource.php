<?php

namespace App\Filament\Resources\Articles;

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string | \UnitEnum | null $navigationGroup = 'CMS & Konten';

    protected static ?string $navigationLabel = 'Artikel & Edukasi';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Daftar Artikel';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Artikel')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('URL Slug')
                    ->required()
                    ->unique(Article::class, 'slug', ignoreRecord: true)
                    ->maxLength(255),

                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'tips' => 'Tips & Trik Sewa Kost',
                        'guide' => 'Panduan Pemilik & Manajemen',
                        'lifestyle' => 'Gaya Hidup & Kampus',
                        'news' => 'Berita & Pengumuman',
                    ])
                    ->required(),

                FileUpload::make('cover_image')
                    ->label('Foto Sampul / Banner')
                    ->directory('articles')
                    ->disk('public')
                    ->image()
                    ->nullable(),

                Textarea::make('excerpt')
                    ->label('Ringkasan Singkat (Excerpt)')
                    ->rows(2)
                    ->nullable(),

                RichEditor::make('body')
                    ->label('Isi Artikel')
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('is_published')
                    ->label('Publikasikan')
                    ->default(true),

                TextInput::make('meta_title')
                    ->label('SEO Meta Title')
                    ->nullable(),

                Textarea::make('meta_description')
                    ->label('SEO Meta Description')
                    ->rows(2)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tips' => 'success',
                        'guide' => 'primary',
                        'lifestyle' => 'warning',
                        default => 'secondary',
                    }),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
