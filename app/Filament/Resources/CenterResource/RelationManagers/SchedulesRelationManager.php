<?php

namespace App\Filament\Resources\CenterResource\RelationManagers;

use App\Forms\Components\BooleanField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'جدول المواعيد';
    protected static ?string $label = 'جدول';
    protected static ?string $pluralLabel = 'جداول المواعيد';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day')
                    ->label('اليوم')
                    ->native(false)
                    ->required()
                    ->columnSpanFull()
                    ->options([
                        'saturday' => 'السبت',
                        'sunday' => 'الأحد',
                        'monday' => 'الإثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                    ]),
                Forms\Components\TimePicker::make('start_time')
                    ->label('وقت البداية')
                    ->displayFormat('h:i a')
                    ->default('09:00 am')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('وقت النهاية')
                    ->displayFormat('h:i a')
                    ->default('05:00 pm')
                    ->required(),
                BooleanField::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('day')
                    ->label('اليوم')
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'saturday' => 'السبت',
                        'sunday' => 'الأحد',
                        'monday' => 'الإثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('وقت البداية')
                    ->sortable()
                    ->alignCenter()
                    ->dateTime('h:i a'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('وقت النهاية')
                    ->sortable()
                    ->alignCenter()
                    ->dateTime('h:i a'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
