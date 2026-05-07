<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * ProductResource — Halaman admin Filament untuk mengelola produk.
 *
 * Resource ini menangani CRUD produk di panel admin (/admin/products).
 * Admin bisa membuat, mengedit, dan menghapus produk lengkap dengan
 * gambar dan varian (ukuran & stok) dari satu halaman form.
 *
 * Navigasi: Catalog → Products (icon: shopping-bag, urutan ke-3)
 */
class ProductResource extends Resource
{
    /** Model yang dikelola oleh resource ini */
    protected static ?string $model = Product::class;

    /** Icon navigasi di sidebar admin */
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    /** Grup navigasi di sidebar (dikelompokkan bersama Category & Brand) */
    protected static string | UnitEnum | null $navigationGroup = 'Catalog';

    /** Urutan tampil di dalam grup navigasi */
    protected static ?int $navigationSort = 3;

    /**
     * Form — Mendefinisikan form untuk create/edit produk.
     *
     * Form dibagi menjadi 3 section:
     * 1. Product Information → Nama, slug, deskripsi, harga, kategori, brand.
     * 2. Product Images      → Upload gambar produk (bisa banyak via Repeater).
     * 3. Variants            → Ukuran dan stok (bisa banyak via Repeater).
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Section 1: Informasi dasar produk
            Section::make('Product Information')->schema([
                // Nama produk — saat diketik, slug otomatis ter-generate
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // Update slug saat blur (keluar dari field)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                // Slug — URL-friendly name, harus unik
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true), // Abaikan record saat ini (saat edit)

                // Deskripsi produk (textarea panjang)
                Textarea::make('description')
                    ->maxLength(5000)
                    ->columnSpanFull(),

                // Harga produk dalam Rupiah
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),

                // Harga diskon (opsional)
                TextInput::make('discount_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->helperText('Optional. Leave empty for no discount.'),

                // Dropdown kategori — diambil dari relasi, bisa search
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                // Dropdown brand — diambil dari relasi, bisa search
                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ])->columns(2), // Layout 2 kolom

            // Section 2: Upload gambar produk (repeater = bisa tambah banyak)
            Section::make('Product Images')->schema([
                Repeater::make('images')
                    ->relationship() // Otomatis save ke tabel product_images
                    ->schema([
                        FileUpload::make('image_url')
                            ->image()
                            ->directory('products')  // Disimpan di storage/app/public/products/
                            ->disk('public')
                            ->required(),
                    ])
                    ->minItems(1)     // Minimal 1 gambar
                    ->defaultItems(1) // Default 1 slot upload
                    ->columnSpanFull(),
            ]),

            // Section 3: Varian produk (ukuran dan stok)
            Section::make('Variants (Size & Stock)')->schema([
                Repeater::make('variants')
                    ->relationship() // Otomatis save ke tabel product_variants
                    ->schema([
                        TextInput::make('size')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    /**
     * Table — Mendefinisikan tampilan tabel daftar produk di admin.
     *
     * Kolom: Gambar, Nama, Harga, Kategori, Brand, Jumlah Varian, Tanggal.
     * Filter: Berdasarkan Kategori dan Brand.
     * Aksi: Edit dan Hapus per record, Bulk Delete.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Gambar produk (circular, ambil dari relasi images)
                ImageColumn::make('images.image_url')
                    ->label('Image')
                    ->disk('public')
                    ->circular()
                    ->limit(1), // Hanya tampilkan 1 gambar

                TextColumn::make('name')->searchable()->sortable(),

                // Harga dalam format Rupiah
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('category.name')->sortable(),
                TextColumn::make('brand.name')->sortable(),

                // Jumlah varian (dihitung otomatis)
                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default
            ])
            ->filters([
                // Filter dropdown berdasarkan kategori
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                // Filter dropdown berdasarkan brand
                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brand'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\CreateAction::make(),
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Halaman yang tersedia untuk resource ini:
     * - List   → Daftar semua produk (/admin/products)
     * - Create → Form tambah produk baru (/admin/products/create)
     * - Edit   → Form edit produk (/admin/products/{id}/edit)
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
