<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Forms\Components\BooleanField;
use App\Forms\Components\Selector;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'fas-user-injured';

    protected static ?string $label = "مريض";
    protected static ?string $pluralLabel = "المرضى";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة المريض')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->alignCenter()
                            ->directory('patients')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('file_number')
                            ->label('رقم الملف')
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_id')
                            ->label('رقم الوطني')
                            ->required()
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->maxLength(12)
                            ->minLength(12)
                            ->rule('regex:/^[1-2][0-9]{11}$/')
                            ->validationMessages([
                                'regex' => 'ضيغة الرقم الوطني غير صحيحة',
                                'unique' => 'الرقم الوطني مسجل مسبقاً',
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('family_issue_number')
                            ->label('رقم قيد العائلة')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(10)
                            ->required()
                            ->minLength(10)
                            ->suffixIcon('fas-phone-alt')
                            ->validationMessages([
                                'regex' => 'ضيغة رقم الهاتف غير صحيحة',
                                'unique' => 'رقم الهاتف مسجل مسبقاً',
                                'max_digits' => 'رقم الهاتف يجب أن يتكون من 10 أرقام',
                            ])
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->rule('regex:/^09[1-5][0-9]{7}$/'),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->suffixIcon('fas-envelope')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->disabled(fn(string $context) => $context === 'edit')
                            ->required()
                            ->maxLength(255),
                        Selector::make('center_id')
                            ->label('المركز')
                            ->required()
                            ->validationMessages([
                                'required' => 'المركز مطلوب',
                            ])
                            ->relationship('center', 'name'),
                    ])->columns(),
                Forms\Components\Section::make('المعلومات الإضافية')
                    ->schema([
                        Forms\Components\Select::make('gender')
                            ->label('جنس المريض')
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ])->native(false),
                        Forms\Components\DatePicker::make('dob')
                            ->label('تاريخ الميلاد')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('blood_group')
                            ->label('فصيلة الدم')
                            ->native(false)
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ]),
                        BooleanField::make('verified')
                            ->label('مستوفي البيانات')
                            ->default(false)
                            ->required(),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_number')
                    ->label('رقم الملف')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->label('الرقم الوطني')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('family_issue_number')
                    ->label('قيد العائلة')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('الجنس')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    })->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dob')
                    ->label('تاريخ الميلاد')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('blood_group')
                    ->label('فصيلة الدم')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('صورة المريض')
                    ->alignCenter()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('verified')
                    ->label('مستوفي البيانات')
                    ->alignCenter()
                    ->boolean(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('تأكيد')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Patient $record) => $record->update(['verified' => true]))
                        ->visible(fn(Patient $record) => !$record->verified)
                        ->icon('fas-check-circle'),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
