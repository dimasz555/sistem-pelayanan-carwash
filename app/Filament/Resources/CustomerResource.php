<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Exports\CustomerExporter;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $pluralModelLabel = 'Kelola Pelanggan';
    protected static ?string $modelLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Data Master';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('sapaan')
                    ->label('Sapaan')
                    ->options([
                        'Pak' => 'Pak',
                        'Bu' => 'Bu',
                        'Bang' => 'Bang',
                        'Kak' => 'Kak',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->required()
                    ->maxLength(15)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('address')
                    ->label('Alamat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_wash')
                    ->label('Total Pencucian')
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('free_wash_count')
                    ->label('Jumlah Cuci Gratis')
                    ->visibleOn('edit')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sapaan')
                    ->label('Sapaan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('total_wash')
                    ->label('Total Pencucian')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('free_wash_count')
                    ->label('Jumlah Cuci Gratis')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('frequent_customer')
                    ->query(fn(Builder $query): Builder => $query->where('total_wash', '>=', 5))
                    ->label('Pelanggan Setia (5+ cuci)'),

                Tables\Filters\Filter::make('has_free_wash')
                    ->query(fn(Builder $query): Builder => $query->where('free_wash_count', '>', 0))
                    ->label('Menggunakan Layanan Cuci Gratis'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_transactions')
                    ->icon('heroicon-o-eye') // Ikon mata untuk view
                    ->iconButton() // Menghilangkan label teks dan hanya menampilkan ikon
                    ->tooltip('Lihat Riwayat Transaksi') // Tooltip saat hover
                    ->color('gray') // Menggunakan warna abu-abu default
                    ->modalHeading(fn(Customer $record): string => 'Riwayat Transaksi - ' . $record->name)
                    ->modalContent(function (Customer $record) {
                        $transactions = $record->transactions()->with('service')->latest()->limit(10)->get();

                        return view('filament.pages.transaction-history', [
                            'transactions' => $transactions,
                            'customer' => $record,
                        ]);
                    })
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->slideOver(),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit Customer'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus Customer'),
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
                                new CustomerExporter($records),
                                'Laporan Pelanggan_' . now()->format('Y-m-d') . '.xlsx'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
