<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * UserResource — Halaman admin Filament untuk melihat data user.
 *
 * Resource ini bersifat READ-ONLY (hanya View, tidak ada Create/Edit/Delete).
 * Admin bisa melihat daftar user beserta jumlah pesanan mereka.
 * Navigasi: Users → Users (icon: users)
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | UnitEnum | null $navigationGroup = 'Users';

    /**
     * Tabel daftar user — nama, email, jumlah order, tanggal daftar.
     * Hanya ada aksi View (tidak ada edit/delete untuk keamanan).
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                // Menghitung jumlah pesanan per user
                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
