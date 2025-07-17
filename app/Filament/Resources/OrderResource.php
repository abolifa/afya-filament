<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'fas-shopping-cart';


    protected static ?string $label = "طلب";
    protected static ?string $pluralLabel = "الطلبات";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\ToggleButtons::make('type')
                        ->label('نوع الطلب')
                        ->options([
                            'in' => 'طلب مخزون',
                            'out' => 'صرف لمريض',
                            'transfer' => 'تحويل مخزون',
                        ])
                        ->inline()
                        ->grouped()
                        ->reactive()
                        ->columnSpan(1)
                        ->required(),
                    Selector::make('center_id')
                        ->label('المركز')
                        ->relationship('center', 'name')
                        ->columnSpan(2)
                        ->default(auth()->user()?->center_id)
                        ->required(),
                    Selector::make('patient_id')
                        ->label('المريض')
                        ->disabled(fn($get) => $get('type') !== 'out')
                        ->required(fn($get) => $get('type') === 'out')
                        ->relationship('patient', 'name'),
                    Selector::make('supplier_id')
                        ->label('المورد')
                        ->disabled(fn($get) => $get('type') !== 'in')
                        ->relationship('supplier', 'name'),
                    Selector::make('to_center_id')
                        ->label('المركز المحول إليه')
                        ->disabled(fn($get) => $get('type') !== 'transfer')
                        ->required(fn($get) => $get('type') === 'transfer')
                        ->relationship('toCenter', 'name'),
                    Selector::make('appointment_id')
                        ->label('رقم الموعد')
                        ->disabled(fn($get) => $get('type') !== 'out')
                        ->relationship('appointment', 'id'),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->columnSpan(2)
                        ->native(false)
                        ->options([
                            'pending' => 'قيد الانتظار',
                            'confirmed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                        ])
                        ->default('pending')
                        ->required(fn(string $context) => $context === 'edit')
                        ->disabled(fn(string $context) => $context === 'create'),
                ])->columns(3),


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
                                    ->numeric()
                                    ->required()
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
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الطلب')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in' => 'طلب مخزون',
                        'out' => 'صرف لمريض',
                        'transfer' => 'تحويل مخزون',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'in' => 'success',
                        'out' => 'warning',
                        'transfer' => 'info',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('المصدر')
                    ->badge()
                    ->alignCenter()
                    ->getStateUsing(fn(Order $record) => match ($record->type) {
                        'in' => $record->supplier?->name ?? '-',
                        'out', 'transfer' => $record->center?->name ?? '-',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('destination')
                    ->label('الوجهة')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->getStateUsing(fn(Order $record) => match ($record->type) {
                        'in', 'transfer' => $record->center?->name ?? '-',
                        'out' => $record->patient?->name ?? '-',
                        default => '-',
                    }),
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

//                Searchable Hidden fields
                Tables\Columns\TextColumn::make('center.name')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toCenter.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
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
