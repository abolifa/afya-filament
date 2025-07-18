<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'fas-box';

    protected static ?string $label = "منتج";
    protected static ?string $pluralLabel = "الأدوية والمعدات";

    protected static ?string $navigationGroup = "إدارة المخزون";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم المنتج')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->options([
                            'medicine' => 'دواء',
                            'equipment' => 'معدات طبية',
                            'service' => 'خدمة طبية',
                            'other' => 'أخرى',
                        ])
                        ->label('نوع المنتج')
                        ->native(false)
                        ->required(),
                    Forms\Components\FileUpload::make('image')
                        ->label('صورة المنتج')
                        ->imageEditor()
                        ->directory('products')
                        ->columnSpanFull()
                        ->image(),
                    Forms\Components\DatePicker::make('expiry_date')
                        ->label('تاريخ انتهاء الصلاحية')
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\TextInput::make('alert_threshold')
                        ->label('حد التنبيه')
                        ->required()
                        ->numeric()
                        ->default(10),
                    Forms\Components\RichEditor::make('description')
                        ->label('وصف المنتج')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('usage')
                        ->label('طريقة الاستخدام')
                        ->columnSpanFull(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('صورة المنتج')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'medicine' => 'دواء',
                        'equipment' => 'معدات طبية',
                        'service' => 'خدمة طبية',
                        'other' => 'أخرى',
                    })->badge()
                    ->alignCenter()
                    ->sortable()
                    ->color(fn($state) => match ($state) {
                        'medicine' => 'info',
                        'equipment' => 'warning',
                        'service' => 'success',
                        'other' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('الصلاحية')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alert_threshold')
                    ->label('حد التنبيه')
                    ->placeholder('-')
                    ->alignCenter()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
