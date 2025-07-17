<?php

namespace App\Forms\Components;

use Filament\Forms\Components\ToggleButtons;

class BooleanField extends ToggleButtons
{
    public function setUp(): void
    {
        parent::setUp();

        $this->label('نشط')
            ->default(true)
            ->inline()
            ->boolean()
            ->grouped();
    }
}
