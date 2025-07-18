<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferInvoiceResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Product;
use App\Models\TransferInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferInvoiceResource extends Resource
{
    protected static ?string $model = TransferInvoice::class;

    protected static ?string $navigationIcon = 'bx-transfer';

    protected static ?string $label = "تحويل";
    protected static ?string $pluralLabel = "تحويل مخزون";

    protected static ?string $navigationGroup = "إدارة المخزون";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Selector::make('from_center_id')
                        ->label('من مركز')
                        ->reactive()
                        ->relationship('fromCenter', 'name')
                        ->required(),
                    Selector::make('to_center_id')
                        ->label('إلى مركز')
                        ->relationship('toCenter', 'name')
                        ->required()
                        ->reactive()
                        ->rules([
                            'different:from_center_id',
                        ])
                        ->validationMessages([
                            'to_center_id.different' => 'لا يمكن اختيار نفس المركز كمصدر ووجهة.',
                        ]),
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
                                    ->rules(fn($get) => collect([
                                        'required',
                                        'numeric',
                                        'min:1',
                                        $get('product_id')
                                            ? 'max:' . Product::find($get('product_id'))
                                                ->stockInCenter(
                                                    $get('../../from_center_id')
                                                    ?? auth()->user()?->center_id
                                                )
                                            : null,
                                    ])->filter()->all())
                                    ->validationMessages([
                                        'max' => 'الكمية تتجاوز المخزون المتاح في المركز.',
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fromCenter.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toCenter.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransferInvoices::route('/'),
            'create' => Pages\CreateTransferInvoice::route('/create'),
            'edit' => Pages\EditTransferInvoice::route('/{record}/edit'),
        ];
    }
}
