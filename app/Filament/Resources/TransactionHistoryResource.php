<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionHistoryResource\Pages;
use App\Filament\Resources\TransactionHistoryResource\RelationManagers;
use Illuminate\Support\Carbon;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Exports\TransactionExporter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class TransactionHistoryResource extends Resource
{
    protected static ?string $model = Transaction::class; // Model Transaction yang sama

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $modelLabel = 'Riwayat Transaksi';
    protected static ?string $pluralModelLabel = 'Riwayat Transaksi';

    // public static function canViewAny(): bool
    // {
    //     return Auth::user() && (Auth::user()->hasRole('kasir') || Auth::user()->hasRole('super_admin'));
    // }

    // public static function canAccess(): bool
    // {
    //     return Auth::user() && (Auth::user()->hasRole('kasir') || Auth::user()->hasRole('super_admin'));
    // }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'service'])
            ->orderBy('transaction_at', 'asc');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form hanya untuk view/edit (tidak ada create)
            // Bisa menggunakan form yang sama atau yang disederhanakan
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
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('queue_number')
                    ->label('Antrian')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('invoice')
                    ->label('Invoice')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Plat Nomor')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vehicle_name')
                    ->label('Nama Kendaraan')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'menunggu' => 'Menunggu',
                            'proses' => 'Proses',
                            'selesai' => 'Selesai',
                            'batal' => 'Batal',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->colors([
                        'warning' => 'menunggu',
                        'primary' => 'proses',
                        'success' => 'selesai',
                        'danger' => 'batal',
                    ]),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Status Pembayaran')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_free')
                    ->label('Cuci Gratis')
                    ->boolean(),

                Tables\Columns\TextColumn::make('cashier_name')
                    ->label('Kasir')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Waktu Pembayaran')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'proses' => 'Proses',
                        'selesai' => 'Selesai',
                    ]),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Status Bayar')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Bayar')
                    ->falseLabel('Belum Bayar'),

                Tables\Filters\TernaryFilter::make('is_free')
                    ->label('Cuci Gratis')
                    ->placeholder('Semua')
                    ->trueLabel('Cuci Gratis')
                    ->falseLabel('Cuci Berbayar'),

                Tables\Filters\SelectFilter::make('cashier_name')
                    ->label('Kasir')
                    ->options(function () {
                        return Transaction::distinct()
                            ->pluck('cashier_name', 'cashier_name')
                            ->toArray();
                    }),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Laporan')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            return Excel::download(
                                new TransactionExporter($records),
                                'Laporan Transaksi_' . now()->format('Y-m-d') . '.xlsx'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('transaction_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionHistories::route('/'),
        ];
    }
}
