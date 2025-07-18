<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Prescription;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static ?string $navigationIcon = 'gameicon-medicines';

    protected static ?string $navigationGroup = "إدارة المرضى";


    protected static ?string $label = "وصفة طبية";
    protected static ?string $pluralLabel = "الوصفات الطبية";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Selector::make('patient_id')
                        ->label('المريض')
                        ->relationship('patient', 'name')
                        ->required(),
                    Selector::make('doctor_id')
                        ->label('الطبيب')
                        ->relationship('doctor', 'name')
                        ->required(),
                    Selector::make('appointment_id')
                        ->label('الموعد')
                        ->relationship('appointment', 'id'),
                    Forms\Components\DatePicker::make('date')
                        ->label('تاريخ الوصفة')
                        ->displayFormat('d/m/Y')
                        ->default(Carbon::now())
                        ->required(),
                    Forms\Components\RichEditor::make('notes')
                        ->label('ملاحظات')
                        ->columnSpanFull(),
                ])->columns(),

                Forms\Components\Section::make('الأدوية')->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->label('سطر وصفة')
                        ->columnSpanFull()
                        ->minItems(1)
                        ->collapsible()
                        ->reorderable()
                        ->columns()
                        ->schema([
                            Selector::make('product_id')
                                ->label('الدواء')
                                ->relationship('product', 'name')
                                ->required(),

                            Forms\Components\Select::make('frequency')
                                ->label('التكرار')
                                ->options([
                                    'daily' => 'يومي',
                                    'weekly' => 'أسبوعي',
                                    'monthly' => 'شهري',
                                ])
                                ->default('daily')
                                ->required(),

                            Forms\Components\TextInput::make('interval')
                                ->label('الفاصل الزمني (الفترة)')
                                ->numeric()
                                ->default(1)
                                ->required(),

                            Forms\Components\TextInput::make('times_per_interval')
                                ->label('عدد الجرعات في الفترة')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            Forms\Components\TextInput::make('dose_amount')
                                ->label('مقدار الجرعة')
                                ->numeric()
                                ->required(),
                            Forms\Components\Select::make('dose_unit')
                                ->label('وحدة الجرعة')
                                ->options([
                                    'mg' => 'ملغم',
                                    'ml' => 'ملل',
                                    'tablet' => 'قرص',
                                    'capsule' => 'كبسولة',
                                    'unit' => 'وحدة',
                                    'drop' => 'قطرة',
                                ])
                                ->required(),
                            Forms\Components\DatePicker::make('start_date')
                                ->label('بداية المدة')
                                ->displayFormat('d/m/Y')
                                ->default(now())
                                ->required(),
                            Forms\Components\DatePicker::make('end_date')
                                ->label('نهاية المدة')
                                ->displayFormat('d/m/Y')
                                ->nullable(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPrescriptions::route('/'),
            'create' => Pages\CreatePrescription::route('/create'),
            'edit' => Pages\EditPrescription::route('/{record}/edit'),
        ];
    }
}
