<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Exception;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'gmdi-stacked-line-chart-o';

    protected static ?string $pluralLabel = "حركة المخزون";

    protected static ?string $navigationGroup = "إدارة المخزون";


    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in' => 'فاتورة مشتريات',
                        'out' => 'طلب مريض',
                        'transfer' => 'تحويل مخزون',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'in' => 'success',
                        'out' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->label('نوع الطلب'),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('بواسطة')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('رقم الطلب')
                    ->badge()
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromCenter.name')
                    ->label('من المركز')
                    ->numeric()
                    ->alignCenter()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('toCenter.name')
                    ->label('إلي المركز')
                    ->placeholder('-')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('إلى المريض')
                    ->numeric()
                    ->placeholder('-')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('من المورد')
                    ->placeholder('-')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('بتاريخ')
                    ->alignCenter()
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'فاتورة مشتريات',
                        'out' => 'طلب مريض',
                        'transfer' => 'تحويل مخزون',
                    ])
                    ->native(false)
                    ->label('نوع الطلب'),
                Tables\Filters\SelectFilter::make('from_center_id')
                    ->relationship('fromCenter', 'name')
                    ->label('من المركز')
                    ->native(false),
                Tables\Filters\SelectFilter::make('to_center_id')
                    ->relationship('toCenter', 'name')
                    ->label('إلى المركز')
                    ->native(false),
                Tables\Filters\SelectFilter::make('patient_id')
                    ->relationship('patient', 'name')
                    ->label('إلى المريض')
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }
}
