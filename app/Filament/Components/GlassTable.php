<?php

namespace App\Filament\Components;

use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class GlassTable extends Table
{
    protected function getTableView(): string
    {
        return 'filament.components.glass-table';
    }

    public function render(): View
    {
        return view($this->getTableView(), [
            'table' => $this,
        ])->with([
            'extraAttributes' => [
                'class' => 'glass-card rounded-xl overflow-hidden'
            ]
        ]);
    }
}