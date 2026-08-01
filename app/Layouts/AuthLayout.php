<?php
declare(strict_types=1);

namespace App\Layouts;

use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Layouts\Components\Outlet;
use Lattice\Lattice\Layouts\LayoutDefinition;
use Lattice\Lattice\Ui\Components\Icon;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\Height;
use Lattice\Lattice\Ui\Enums\Justify;
use Lattice\Lattice\Ui\Enums\Size;
use Lattice\Lattice\Ui\Enums\Width;

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
