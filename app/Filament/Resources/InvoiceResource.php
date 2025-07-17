<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'fas-file-invoice-dollar';


    protected static ?string $label = "فاتورة";
    protected static ?string $pluralLabel = "فواتير المشتريات";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Selector::make('center_id')
                        ->label('المركز')
                        ->required()
                        ->relationship('center', 'name'),
                    Selector::make('supplier_id')
                        ->label('المورد')
                        ->relationship('supplier', 'name'),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->native(false)
                        ->options([
                            'pending' => 'قيد الانتظار',
                            'confirmed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                        ])
                        ->default('pending')
                        ->required(fn(string $context) => $context === 'edit')
                        ->disabled(fn(string $context) => $context === 'create'),
                ])->columns(),


                Forms\Components\Section::make('الأصناف')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->columns()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->required()
                            ->validationMessages([
                                'required' => 'يجب إضافة صنف واحد على الأقل.',
                            ])
                            ->addActionLabel('إضافة صنف')
                            ->schema([
                                Selector::make('product_id')
                                    ->label('المنتج')
                                    ->relationship('product', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('confirm')
                        ->label('تأكيد')
                        ->icon('fas-check-circle')
                        ->action(function (Invoice $record) {
                            $record->update(['status' => 'confirmed']);
                        })
                        ->color('success')
                        ->visible(fn(Invoice $record) => $record->status === 'pending')
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cancel')
                        ->label('إلغاء')
                        ->icon('fas-times-circle')
                        ->action(function (Invoice $record) {
                            $record->update(['status' => 'cancelled']);
                        })
                        ->color('danger')
                        ->visible(fn(Invoice $record) => $record->status === 'pending')
                        ->requiresConfirmation(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
