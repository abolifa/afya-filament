<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Forms\Components\Selector;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'fas-user-doctor';

    protected static ?string $label = "طبيب";
    protected static ?string $pluralLabel = "الأطباء";

    protected static ?string $navigationGroup = "إدارة الموارد";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم')
                        ->required()
                        ->maxLength(255),
                    Selector::make('center_id')
                        ->label('المركز')
                        ->relationship('center', 'name')
                        ->validationMessages([
                            'required' => 'المركز مطلوب',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('specialization')
                        ->label('التخصص')
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
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->revealable()
                        ->required(fn(string $context) => $context === 'create')
                        ->disabled(fn(string $context) => $context === 'edit')
                        ->required()
                        ->maxLength(255),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialization')
                    ->label('التخصص')
                    ->sortable()
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
