<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Filament\Resources\PromoResource\RelationManagers;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Kelola Promo';
    protected static ?string $modelLabel = 'Promo';
    protected static ?string $pluralModelLabel = 'Kelola Promo';
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
                Forms\Components\Section::make('Informasi Promo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Promo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Diskon Weekend'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi promo untuk memberikan info lebih detail')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pengaturan Diskon')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai Diskon (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->step(0.01)
                            ->helperText('Masukkan nilai diskon dalam persen (misal: 20 untuk diskon 20%)'),
                    ]),

                Forms\Components\Section::make('Periode Promo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->default(now())
                                    ->beforeOrEqual('end_date'),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Tanggal Berakhir')
                                    ->required()
                                    ->default(now()->addDays(30))
                                    ->afterOrEqual('start_date'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Promo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai Diskon')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->getStateUsing(function ($record) {
                        return \Carbon\Carbon::parse($record->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($record->end_date)->format('d/m/Y');
                    })
                    ->sortable(['start_date']),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_computed')
                    ->label('Ketersediaan')
                    ->getStateUsing(function ($record) {
                        if (!$record->is_active) return 'Nonaktif';

                        $today = now()->toDateString();
                        if ($today < $record->start_date) return 'Belum Mulai';
                        if ($today > $record->end_date) return 'Berakhir';

                        return 'Tersedia';
                    })
                    ->colors([
                        'success' => 'Tersedia',
                        'warning' => 'Belum Mulai',
                        'danger' => fn($state) => in_array($state, ['Nonaktif', 'Berakhir']),
                    ]),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)
                            ->timezone('Asia/Jakarta')
                            ->locale('id')
                            ->isoFormat('dddd, D MMMM YYYY, HH:mm');

                        return '';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('is_active')
                    ->label('Status Aktif')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true)),

                Tables\Filters\Filter::make('available')
                    ->label('Tersedia Sekarang')
                    ->query(fn(Builder $query): Builder => $query->active()),

                Tables\Filters\Filter::make('expired')
                    ->label('Berakhir')
                    ->query(fn(Builder $query): Builder => $query->where('end_date', '<', now())),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Akan Datang')
                    ->query(fn(Builder $query): Builder => $query->where('start_date', '>', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
