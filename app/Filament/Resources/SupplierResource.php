<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'fas-store';

    protected static ?string $label = "مورد";
    protected static ?string $pluralLabel = "الموردين";

    protected static ?string $navigationGroup = "إدارة الموارد";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم المورد')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label('العنوان')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->alignCenter()
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->alignCenter()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true)
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
        ];
    }
}
