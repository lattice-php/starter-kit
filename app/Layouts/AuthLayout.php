<?php
declare(strict_types=1);

namespace App\Layouts;

use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsLayout;
use Lattice\Lattice\Core\Components\Icon;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Enums\Align;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\Height;
use Lattice\Lattice\Core\Enums\Justify;
use Lattice\Lattice\Core\Enums\Size;
use Lattice\Lattice\Core\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Layouts\Components\Outlet;
use Lattice\Lattice\Layouts\LayoutDefinition;

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
