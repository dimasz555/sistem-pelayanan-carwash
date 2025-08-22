<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QueueCompletedResource\Pages;
use App\Filament\Resources\QueueCompletedResource\RelationManagers;
use App\Models\QueueCompleted;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class QueueCompletedResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralModelLabel = 'Antrian Selesai';
    protected static ?string $modelLabel = 'Antrian Selesai';
    protected static ?string $navigationGroup = 'Antrian';

    public static function canAccess(): bool
    {
        return Auth::user() && (Auth::user()->hasRole('koordinator') || Auth::user()->hasRole('super_admin'));
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('status', 'selesai')
            ->with(['customer', 'service'])
            ->whereDate('transaction_at', Carbon::today())
            ->orderBy('done_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_at')
                    ->label('Tanggal')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    }),

                Tables\Columns\TextColumn::make('queue_number')
                    ->label('No. Antrian')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('invoice')
                    ->label('Invoice')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Plat Nomor')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('vehicle_name')
                    ->label('Kendaraan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'proses' => 'info',
                        'selesai' => 'success',
                        'batal' => 'danger'
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'proses' => 'Proses',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    }),

                Tables\Columns\TextColumn::make('waiting_at')
                    ->label('Waktu Menunggu')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('processing_at')
                    ->label('Waktu Diproses')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('done_at')
                    ->label('Waktu Selesai')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Status Pembayaran')
                    ->trueLabel('Sudah Lunas')
                    ->falseLabel('Belum Lunas')
                    ->native(false),
            ])
            ->actions([])
            ->bulkActions([])
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQueueCompleteds::route('/'),
            'create' => Pages\CreateQueueCompleted::route('/create'),
            'edit' => Pages\EditQueueCompleted::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()
            ->whereDate('transaction_at', Carbon::today())
            ->where('status', 'selesai')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
