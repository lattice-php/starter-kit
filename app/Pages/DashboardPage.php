<?php
declare(strict_types=1);

namespace App\Pages;

use App\Concerns\ResolvesCurrentUser;
use App\Http\Middleware\SwitchesCurrentTeam;
use App\Models\Team;
use App\Pages\Concerns\ListensForUserNotifications;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Card;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Http\Page;

#[AsPage(route: '{current_team}/dashboard', name: 'dashboard', layout: PageLayout::App, container: PageContainer::Default, middleware: ['web', 'auth', 'verified', 'can:view,current_team', SwitchesCurrentTeam::class])]
class DashboardPage extends Page
{
    use ListensForUserNotifications;
    use ResolvesCurrentUser;

    private ?Team $team = null;

    public function title(): string
    {
        return 'Dashboard';
    }

    /**
     * @return array<int, array{title: string, href: string}>
     */
    public function breadcrumbs(): array
    {
        if (! $this->team instanceof Team) {
            return [];
        }

        return [
            [
                'title' => 'Dashboard',
                'href' => route('dashboard', ['current_team' => $this->team->slug], absolute: false),
            ],
        ];
    }

    public function render(PageSchema $schema, Request $request, Team $current_team): PageSchema
    {
        $user = $this->currentUser();

        $this->team = $current_team;

        return $schema->schema([
            Stack::make('dashboard-page')
                ->gap(Gap::Large)
                ->width(Width::Large)
                ->schema([
                    Stack::make('dashboard-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make('Dashboard', 1),
                            Text::make('Welcome back, '.$user->name.'. You are viewing '.$current_team->name.'.'),
                        ]),
                    Grid::make('dashboard-overview')
                        ->columns(3)
                        ->schema([
                            Card::make('Server-driven pages', 'This dashboard is rendered from a Lattice page definition.'),
                            Card::make('Team context', 'Routes, breadcrumbs, and layout are resolved on the server.'),
                            Card::make('Composable UI', 'Pages can compose cards, grids, forms, tables, and actions.'),
                        ]),
                    Card::make('Next steps', 'Replace these starter metrics with real team activity as the kit grows.'),
                ]),
        ]);
    }
}
