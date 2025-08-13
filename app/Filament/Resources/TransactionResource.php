<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use Illuminate\Support\Carbon;
use App\Models\Transaction;
use App\Models\Customer;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Kelola Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?int $navigationSort = 1;
    // protected static ?string $recordTitleAttribute = 'invoice';

    public static function canViewAny(): bool
    {
        return Auth::user() && Auth::user()->hasRole('kasir');
    }

    public static function canAccess(): bool
    {
        return Auth::user() && Auth::user()->hasRole('kasir');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['customer', 'service'])
            ->whereDate('transaction_at', Carbon::today())
            ->orderBy('queue_number', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Informasi Kasir dan Invoice
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cashier_name')
                                    ->label('Nama Kasir')
                                    ->default(Auth::user()->name)
                                    ->disabled()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('invoice')
                                    ->visibleOn(['edit', 'view'])
                                    ->disabled()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                // Bagian Data Pelanggan - Modified with proper hydration
                Forms\Components\Section::make('Data Pelanggan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Nomor Telepon Pelanggan')
                                    ->required()
                                    ->tel()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $customer = Customer::where('phone', $state)->first();
                                            if ($customer) {
                                                $set('customer_id', $customer->id);
                                                $set('customer_name', $customer->name);
                                                $set('customer_sapaan', $customer->sapaan);
                                                $set('customer_address', $customer->address);
                                                $set('customer_total_wash', $customer->total_wash);
                                                $set('customer_free_wash_count', $customer->free_wash_count);
                                                $set('is_existing_customer', true);
                                            } else {
                                                $set('customer_id', null);
                                                $set('customer_name', '');
                                                $set('customer_sapaan', '');
                                                $set('customer_address', '');
                                                $set('customer_total_wash', 0);
                                                $set('customer_free_wash_count', 0);
                                                $set('is_existing_customer', false);
                                            }
                                        }
                                    })
                                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                                        // Populate customer data from relationship for edit/view mode
                                        if ($record && $record->customer) {
                                            $set('customer_phone', $record->customer->phone);
                                            $set('customer_name', $record->customer->name);
                                            $set('customer_sapaan', $record->customer->sapaan);
                                            $set('customer_address', $record->customer->address);
                                            $set('customer_total_wash', $record->customer->total_wash);
                                            $set('customer_free_wash_count', $record->customer->free_wash_count);
                                            $set('is_existing_customer', true);
                                        }
                                    })
                                    ->columnSpan(2),

                                Forms\Components\Hidden::make('customer_id'),
                                Forms\Components\Hidden::make('is_existing_customer')
                                    ->default(false)
                                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                                        if ($record && $record->customer) {
                                            $set('is_existing_customer', true);
                                        }
                                    }),

                                // Display total wash info - FIXED VERSION
                                Forms\Components\Placeholder::make('customer_wash_info')
                                    ->label('Riwayat Cuci')
                                    ->content(function (Forms\Get $get) {
                                        if ($get('is_existing_customer')) {
                                            $totalWash = $get('customer_total_wash') ?? 0;
                                            $freeWash = $get('customer_free_wash_count') ?? 0;
                                            $nextWash = $totalWash + 1;
                                            $toNextFree = 10 - ($totalWash % 10);

                                            return new \Illuminate\Support\HtmlString("
                                            <div class='space-y-1 text-sm'>
                                            <div><strong>Pencucian Ke:</strong> {$nextWash}</div>
                                                <div><strong>Total Cuci:</strong> {$totalWash}x</div>
                                                <div><strong>Cuci Gratis:</strong> {$freeWash}x</div>
                                                <div class='text-blue-600'><strong>Sisa untuk cuci gratis:</strong> {$toNextFree}x lagi</div>
                                            </div>
                                        ");
                                        }
                                        return new \Illuminate\Support\HtmlString('<em class="text-gray-500">Pelanggan baru - mulai dari cuci ke-1</em>');
                                    })
                                    ->visibleOn(['create', 'edit'])
                                    ->columnSpan(2),

                                Forms\Components\Hidden::make('customer_total_wash'),
                                Forms\Components\Hidden::make('customer_free_wash_count'),

                                Select::make('customer_sapaan')
                                    ->label('Sapaan')
                                    ->options([
                                        'Pak' => 'Pak',
                                        'Bu' => 'Bu',
                                        'Bang' => 'Bang',
                                        'Kak' => 'Kak',
                                    ])
                                    ->required()
                                    ->disabled(fn(Forms\Get $get) => $get('is_existing_customer'))
                                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                                        if ($record && $record->customer) {
                                            $set('customer_sapaan', $record->customer->sapaan);
                                        }
                                    }),

                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Nama Pelanggan')
                                    ->required()
                                    ->disabled(fn(Forms\Get $get) => $get('is_existing_customer'))
                                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                                        if ($record && $record->customer) {
                                            $set('customer_name', $record->customer->name);
                                        }
                                    }),

                                Forms\Components\Textarea::make('customer_address')
                                    ->label('Alamat')
                                    ->placeholder('Alamat pelanggan (opsional)')
                                    ->disabled(fn(Forms\Get $get) => $get('is_existing_customer'))
                                    ->columnSpan(2)
                                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                                        if ($record && $record->customer) {
                                            $set('customer_address', $record->customer->address);
                                        }
                                    }),
                            ]),
                    ])
                    ->description(
                        fn(Forms\Get $get) =>
                        $get('is_existing_customer')
                            ? '✅ Pelanggan ditemukan - total cuci akan bertambah otomatis'
                            : '➕ Pelanggan baru - mulai dari cuci ke-1'
                    ),

                // Bagian Data Kendaraan
                Forms\Components\Section::make('Data Kendaraan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('plate_number')
                                    ->label('Nomor Plat')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('vehicle_name')
                                    ->label('Nama Kendaraan')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Pilih Layanan')
                    ->schema([
                        Forms\Components\Hidden::make('service_id')
                            ->required(),

                        Forms\Components\Hidden::make('service_price')
                            // ->numeric()
                            ->dehydrated(),

                        ViewField::make('services_view')
                            ->label('')
                            ->view('filament.custom.service-selector'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Diskon & Total')
                    ->schema([
                        // Row untuk Diskon Amount dan Discount Reason
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Diskon (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        // Hanya hitung ulang total jika is_free tidak aktif
                                        if (!$get('is_free')) {
                                            $servicePrice = (float) ($get('service_price') ?? 0);
                                            $discountAmount = (float) ($state ?? 0);
                                            $set('total_price', max(0, $servicePrice - $discountAmount));
                                        }
                                    })
                                    ->disabled(fn(Get $get) => $get('is_free')) // Disable input diskon jika transaksi gratis
                                    ->rules([
                                        fn(Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $servicePrice = (float) ($get('service_price') ?? 0);
                                            $discountValue = (float) ($value ?? 0);
                                            if ($discountValue > $servicePrice) {
                                                $fail('Diskon tidak boleh lebih dari harga layanan.');
                                            }
                                            if ($discountValue < 0) {
                                                $fail('Diskon tidak boleh negatif.');
                                            }
                                        },
                                    ])
                                    ->helperText(function (Get $get): string {
                                        if ($get('is_free')) {
                                            return 'Transaksi gratis - diskon tidak berlaku';
                                        }

                                        $servicePrice = (float) ($get('service_price') ?? 0);
                                        $discountAmount = (float) ($get('discount_amount') ?? 0);

                                        if ($servicePrice > 0 && $discountAmount > 0) {
                                            $percentage = round(($discountAmount / $servicePrice) * 100, 1);
                                            return "Diskon: {$percentage}%";
                                        }

                                        return '';
                                    }),

                                Forms\Components\TextInput::make('discount_reason')
                                    ->label('Jenis Diskon')
                                    ->placeholder('Contoh: Member VIP, Promosi, dll')
                                    ->maxLength(100),
                            ]),

                        // Total Price dengan tampilan yang lebih besar dan menarik - di tengah
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Placeholder::make('total_display')
                                    ->label('TOTAL PEMBAYARAN')
                                    ->content(function (Get $get): \Illuminate\Contracts\Support\Htmlable {
                                        $totalPrice = $get('total_price') ?? 0;
                                        $formattedPrice = 'Rp ' . number_format($totalPrice, 0, ',', '.');

                                        return new \Illuminate\Support\HtmlString(
                                            '<div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%); border-radius: 12px; border: 2px solid #3b82f6; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <div style="font-size: 2rem; font-weight: bold; color: #1e40af; margin-bottom: 8px;">' . $formattedPrice . '</div>
                                <div style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Total yang harus dibayar</div>
                            </div>'
                                        );
                                    })
                                    ->reactive(),
                            ]),

                        // Hidden field untuk menyimpan total_price di database
                        Forms\Components\Hidden::make('total_price')
                            ->default(0),
                    ])
                    ->columns(1),

                // Bagian Detail Transaksi
                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('queue_number')
                                    ->label('Nomor Antrian')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\DateTimePicker::make('transaction_at')
                                    ->label('Waktu Transaksi')
                                    ->required()
                                    ->VisibleOn('edit'),
                                Forms\Components\DateTimePicker::make('waiting_at')
                                    ->label('Waktu Menunggu')
                                    ->VisibleOn('edit'),
                                Forms\Components\DateTimePicker::make('processing_at')
                                    ->label('Waktu Proses')
                                    ->VisibleOn('edit'),
                                Forms\Components\DateTimePicker::make('done_at')
                                    ->label('Waktu Selesai')
                                    ->VisibleOn('edit'),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'menunggu' => 'Menunggu',
                                        'proses' => 'Proses',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->default('menunggu')
                                    ->required(),
                            ]),
                    ]),

                // Bagian Pengaturan Pembayaran
                Forms\Components\Section::make('Pengaturan Pembayaran')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_free')
                                    ->label('Transaksi Gratis')
                                    ->default(false)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state) {
                                            // Jika is_free = true, set total_price menjadi 0
                                            $set('total_price', 0.00);
                                        } else {
                                            // Jika is_free = false, hitung ulang total_price berdasarkan service_price - discount_amount
                                            $servicePrice = (float) ($get('service_price') ?? 0);
                                            $discountAmount = (float) ($get('discount_amount') ?? 0);
                                            $set('total_price', max(0, $servicePrice - $discountAmount));
                                        }
                                    }),

                                Forms\Components\Toggle::make('is_paid')
                                    ->label('Sudah Dibayar')
                                    ->default(false)
                                    ->required(),
                            ]),
                    ]),
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
                    ->label('Nomor Antrian')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice')
                    ->label('Invoice')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Pelanggan')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->customer ? $record->customer->name : '-';
                    })
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_id')
                    ->label('Layanan')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->service ? $record->service->name : '-';
                    })
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Telepon')
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
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'menunggu' => 'Menunggu',
                            'proses' => 'Proses',
                            'selesai' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->colors([
                        'warning' => 'menunggu',
                        'primary' => 'proses',
                        'success' => 'selesai',
                    ]),
                Tables\Columns\IconColumn::make('is_free')
                    ->label('Cuci Gratis')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Status Pembayaran')
                    ->boolean(),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Status Pembayaran')
                    ->trueLabel('Sudah Lunas')
                    ->falseLabel('Belum Lunas')
                    ->native(false),
            ])
            ->actions([
                Action::make('paid_done')
                    ->label('Tandai Sudah Bayar')
                    ->action(function (Transaction $record) {
                        $record->update([
                            'is_paid' => true,
                            'paid_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Transaksi ditandai sebagai lunas.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(Transaction $record) => $record->is_paid == false),
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail'),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit Transaksi'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus Transaksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->whereDate('transaction_at', Carbon::today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
