<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Forms\Components\BooleanField;
use App\Forms\Components\Selector;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'fas-calendar-days';

    protected static ?string $label = "موعد";
    protected static ?string $pluralLabel = "المواعيد";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Selector::make('patient_id')
                        ->label('المريض')
                        ->relationship('patient', 'name')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'pending' => 'قيد الانتظار',
                            'confirmed' => 'مؤكد',
                            'cancelled' => 'ملغي',
                            'completed' => 'مكتمل',
                        ])
                        ->default('pending')
                        ->required(),
                    Selector::make('center_id')
                        ->label('المركز')
                        ->reactive()
                        ->relationship('center', 'name')
                        ->required(),
                    Selector::make('doctor_id')
                        ->label('الطبيب')
                        ->disabled(fn($get) => !$get('center_id'))
                        ->options(function (Forms\Get $get) {
                            $centerId = $get('center_id');
                            if (!$centerId) {
                                return [];
                            }
                            return Doctor::where('center_id', $centerId)
                                ->pluck('name', 'id')
                                ->toArray();
                        }),
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->displayFormat('d/m/Y')
                        ->default(Carbon::now())
                        ->required(),
                    Forms\Components\TimePicker::make('time')
                        ->label('الوقت')
                        ->displayFormat('h:i A')
                        ->default(Carbon::now()->format('h:i'))
                        ->required(),
                    BooleanField::make('intended')
                        ->label('الحضور')
                        ->default(false)
                        ->required(),

                    BooleanField::make('has_order')
                        ->label('طلب منتجات')
                        ->dehydrated()
                        ->reactive()
                        ->default(false),
                ])->columns(3),
                Forms\Components\Section::make('طلب المنتجات')
                    ->schema([
                        Forms\Components\Repeater::make('order_items')
                            ->label('المنتجات')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->columns()
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('إضافة منتج'),
                    ])
                    ->visible(fn($get) => $get('has_order'))
                    ->columns(),
                Forms\Components\Section::make([
                    Forms\Components\RichEditor::make('notes')
                        ->label('ملاحظات')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->badge()
                    ->searchable()
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->alignCenter()
                    ->badge()
                    ->searchable()
                    ->color('info')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('الوقت')
                    ->time('h:i A')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'cancelled' => 'ملغي',
                        'completed' => 'مكتمل',
                        default => 'غير معروف',
                    })
                    ->alignCenter()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'primary',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('intended')
                    ->label('الحضور')
                    ->alignCenter()
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('confirm')
                        ->label('تأكيد')
                        ->icon('fas-check-circle')
                        ->action(function (Appointment $record) {
                            $record->update(['status' => 'confirmed']);

                            if ($record->order) {
                                $record->order->update(['status' => 'confirmed']);
                            }
                        })
                        ->color('success')
                        ->visible(fn(Appointment $record) => $record->status === 'pending')
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cancel')
                        ->label('إلغاء')
                        ->icon('fas-times-circle')
                        ->action(function (Appointment $record) {
                            $record->update(['status' => 'cancelled']);

                            if ($record->order) {
                                $record->order->update(['status' => 'cancelled']);
                            }
                        })
                        ->color('danger')
                        ->visible(fn(Appointment $record) => $record->status === 'pending')
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('complete')
                        ->label('إكمال')
                        ->icon('fas-check-double')
                        ->action(fn(Appointment $record) => $record->update([
                            'status' => 'completed',
                            'intended' => true,
                        ]))
                        ->color('primary')
                        ->visible(fn(Appointment $record) => $record->status === 'confirmed')
                        ->requiresConfirmation(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
