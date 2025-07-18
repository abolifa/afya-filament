<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'fas-shopping-cart';

    protected static ?string $navigationGroup = "إدارة المخزون";


    protected static ?string $label = "طلب";
    protected static ?string $pluralLabel = "طلبات المرضى";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Selector::make('center_id')
                        ->label('المركز')
                        ->relationship('center', 'name')
                        ->default(auth()->user()?->center_id)
                        ->reactive()
                        ->required(),
                    Selector::make('patient_id')
                        ->label('المريض')
                        ->required()
                        ->relationship('patient', 'name'),
                    Selector::make('appointment_id')
                        ->label('رقم الموعد')
                        ->relationship('appointment', 'id'),
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
                                                    $get('../../center_id')
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
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('المريض')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment.id')
                    ->label('رقم الموعد')
                    ->alignCenter()
                    ->placeholder('-')
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
                        ->action(function (Order $record) {
                            $record->update(['status' => 'confirmed']);
                        })
                        ->color('success')
                        ->visible(fn(Order $record) => $record->status === 'pending')
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cancel')
                        ->label('إلغاء')
                        ->icon('fas-times-circle')
                        ->action(function (Order $record) {
                            $record->update(['status' => 'cancelled']);
                        })
                        ->color('danger')
                        ->visible(fn(Order $record) => $record->status === 'pending')
                        ->requiresConfirmation(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
