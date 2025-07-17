<?php

namespace App\Filament\Pages;

use App\Models\Center;
use App\Models\Product;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductStock extends Page implements HasTable, Forms\Contracts\HasForms
{
    use InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'fas-warehouse';
    protected static string $view = 'filament.pages.product-stock';
    protected static ?string $title = 'المخزن';

    public ?int $selectedCenter = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $base = Product::query()->select('products.*');
                if (is_null($this->selectedCenter)) {
                    return $base
                        ->selectRaw(/** @lang sql */ '
                            (
                                SELECT COALESCE(SUM(ii.quantity), 0)
                                FROM invoice_items AS ii
                                JOIN invoices      AS iv ON iv.id = ii.invoice_id
                                WHERE ii.product_id = products.id
                                  AND iv.status     = "confirmed"
                            )
                            -
                            (
                                SELECT COALESCE(SUM(oi.quantity), 0)
                                FROM order_items AS oi
                                JOIN orders      AS o  ON o.id = oi.order_id
                                WHERE oi.product_id   = products.id
                                  AND o.status        = "confirmed"
                            )
                            AS stock
                        ');
                }

                return $base
                    ->selectRaw(/** @lang sql */ '
                        (
                          SELECT COALESCE(SUM(ii.quantity),0)
                          FROM invoice_items   AS ii
                          JOIN invoices        AS iv ON iv.id = ii.invoice_id
                          WHERE ii.product_id = products.id
                            AND iv.center_id  = ?
                            AND iv.status     = "confirmed"
                        )
                        +
                        (
                          SELECT COALESCE(SUM(tii.quantity),0)
                          FROM transfer_invoice_items AS tii
                          JOIN transfer_invoices      AS ti ON ti.id = tii.transfer_invoice_id
                          WHERE tii.product_id   = products.id
                            AND ti.to_center_id  = ?
                            AND ti.status        = "confirmed"
                        )
                        -
                        (
                          SELECT COALESCE(SUM(oi.quantity),0)
                          FROM order_items    AS oi
                          JOIN orders         AS o ON o.id = oi.order_id
                          WHERE oi.product_id = products.id
                            AND o.center_id    = ?
                            AND o.status       = "confirmed"
                        )
                        -
                        (
                          SELECT COALESCE(SUM(tii.quantity),0)
                          FROM transfer_invoice_items AS tii
                          JOIN transfer_invoices      AS ti ON ti.id = tii.transfer_invoice_id
                          WHERE tii.product_id     = products.id
                            AND ti.from_center_id  = ?
                            AND ti.status          = "confirmed"
                        )
                        AS stock
                    ', [
                        $this->selectedCenter,
                        $this->selectedCenter,
                        $this->selectedCenter,
                        $this->selectedCenter,
                    ]);
            })
            ->columns([
                TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'medicine' => 'دواء',
                        'equipment' => 'معدات طبية',
                        'service' => 'خدمة طبية',
                        'other' => 'أخرى',
                    })->badge()->alignCenter()->sortable(),
                TextColumn::make('stock')
                    ->label('المخزون')
                    ->numeric()
                    ->sortable()
                    ->color(fn(int $state, Product $record) => match (true) {
                        $state === 0 => 'danger',
                        $state <= $record->alert_threshold => 'warning',
                        default => 'success',
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->label('المنتج')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->attribute('products.id'),
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'medicine' => 'دواء',
                        'equipment' => 'معدات طبية',
                        'service' => 'خدمة طبية',
                        'other' => 'أخرى',
                    ])
                    ->native(false)
                    ->attribute('products.type'),
            ], layout: FiltersLayout::Dropdown);
    }

    /**
     * @throws Exception
     */

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make([
                Select::make('selectedCenter')
                    ->label('اختر المركز')
                    ->options(Center::pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->columnSpanFull()
                    ->placeholder('جميع المراكز')
                    ->afterStateUpdated(fn() => $this->resetTable()),
            ])->columns(),
        ];
    }
}
