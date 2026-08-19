<?php
declare(strict_types=1);

namespace App\Layouts;

use Illuminate\Http\Request;
use Lattice\Core\Attributes\AsLayout;
use Lattice\Layouts\Components\Outlet;
use Lattice\Layouts\LayoutDefinition;
use Lattice\Ui\Components\Icon;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Enums\Align;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Height;
use Lattice\Ui\Enums\Justify;
use Lattice\Ui\Enums\Size;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsLayout('auth')]
class AuthLayout extends LayoutDefinition
{
    public function schema(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('auth-shell')
                ->height(Height::Screen)
                ->justify(Justify::Center)
                ->align(Align::Center)
                ->schema([
                    Stack::make('auth-card')
                        ->width(Width::Small)
                        ->align(Align::Center)
                        ->gap(Gap::Large)
                        ->schema([
                            Icon::make('logo')->size(Size::Xl4),
                            Outlet::make(),
                        ]),
                ]),
        ]);
    }
}
