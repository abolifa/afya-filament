<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CenterResource\Pages;
use App\Filament\Resources\CenterResource\RelationManagers\SchedulesRelationManager;
use App\Models\Center;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CenterResource extends Resource
{
    protected static ?string $model = Center::class;

    protected static ?string $navigationIcon = 'fas-building-ngo';

    protected static ?string $label = "مركز";
    protected static ?string $pluralLabel = "المراكز";

    protected static ?string $navigationGroup = "إدارة الموارد";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم المركز')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(10)
                        ->required()
                        ->minLength(10)
                        ->validationMessages([
                            'regex' => 'ضيغة رقم الهاتف غير صحيحة',
                            'unique' => 'رقم الهاتف مسجل مسبقاً',
                            'max_digits' => 'رقم الهاتف يجب أن يتكون من 10 أرقام',
                        ])
                        ->live()
                        ->unique(ignoreRecord: true)
                        ->rule('regex:/^09[1-5][0-9]{7}$/'),
                    Forms\Components\TextInput::make('alt_phone')
                        ->label('رقم الهاتف البديل')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->label('العنوان')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('street')
                        ->label('الشارع')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('city')
                        ->label('المدينة')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('latitude')
                        ->label('خط العرض')
                        ->readOnly()
                        ->numeric(),
                    Forms\Components\TextInput::make('longitude')
                        ->label('خط الطول')
                        ->readOnly()
                        ->numeric(),
                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المركز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('alt_phone')
                    ->label('الهاتف البديل')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->label('الشارع')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('المدينة')
                    ->placeholder('-')
                    ->alignCenter()
                    ->searchable(),
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
            SchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCenters::route('/'),
            'create' => Pages\CreateCenter::route('/create'),
            'edit' => Pages\EditCenter::route('/{record}/edit'),
        ];
    }
}
