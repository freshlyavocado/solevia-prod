<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * OrderResource — Halaman admin Filament untuk mengelola pesanan.
 *
 * Admin bisa melihat, mengedit status, dan memfilter pesanan.
 * Navigasi: Orders → Orders (icon: clipboard-document-list, urutan ke-1)
 */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | UnitEnum | null $navigationGroup = 'Orders';
    protected static ?int $navigationSort = 1;

    /**
     * Form edit pesanan — admin hanya bisa ubah status dan payment_status.
     * Order number dan total amount di-disabled (tidak bisa diubah).
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Info')->schema([
                TextInput::make('order_number')->disabled(),
                TextInput::make('total_amount')->disabled()->prefix('Rp'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Select::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->required(),
            ])->columns(2),
        ]);
    }

    /**
     * Tabel daftar pesanan — menampilkan nomor, customer, total, status, tanggal.
     * Status ditampilkan sebagai badge berwarna.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('user.name')->sortable()->label('Customer'),
                TextColumn::make('total_amount')->money('IDR')->sortable(),
                // Badge status pesanan dengan warna sesuai kondisi
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'shipped' => 'info',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                // Badge status pembayaran
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending', 'paid' => 'Paid',
                    'shipped' => 'Shipped', 'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('payment_status')->options([
                    'unpaid' => 'Unpaid', 'paid' => 'Paid',
                ]),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ]);
    }

    /**
     * Infolist — Halaman detail pesanan (view).
     * Menampilkan info order, shipping, dan payment dalam section terpisah.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Details')->schema([
                TextEntry::make('order_number'),
                TextEntry::make('user.name')->label('Customer'),
                TextEntry::make('total_amount')->money('IDR'),
                TextEntry::make('status')->badge(),
                TextEntry::make('payment_status')->badge(),
                TextEntry::make('created_at')->dateTime(),
            ])->columns(3),

            Section::make('Shipping')->schema([
                TextEntry::make('shipping.recipient_name'),
                TextEntry::make('shipping.phone_number'),
                TextEntry::make('shipping.address'),
                TextEntry::make('shipping.city'),
                TextEntry::make('shipping.province'),
                TextEntry::make('shipping.postal_code'),
            ])->columns(3),

            Section::make('Payment')->schema([
                TextEntry::make('payment.payment_method'),
                TextEntry::make('payment.amount')->money('IDR'),
                TextEntry::make('payment.status')->badge(),
                TextEntry::make('payment.paid_at')->dateTime(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
