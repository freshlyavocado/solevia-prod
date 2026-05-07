<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * BrandResource — Halaman admin Filament untuk mengelola brand/merek.
 *
 * Admin bisa CRUD brand (Nike, Adidas, Puma, New Balance).
 * Termasuk upload logo brand.
 * Navigasi: Catalog → Brands (icon: building-storefront, urutan ke-2)
 */
class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';
    protected static string | UnitEnum | null $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 2;

    /**
     * Form create/edit brand — nama, deskripsi, dan upload logo.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->maxLength(1000)->columnSpanFull(),
            // Upload logo brand ke storage/app/public/brands/
            FileUpload::make('logo_url')
                    ->label('Logo')
                    ->image()
                    ->directory('brands')
                    ->disk('public'),
        ]);
    }

    /**
     * Tabel daftar brand — ID, nama, dan jumlah produk.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
