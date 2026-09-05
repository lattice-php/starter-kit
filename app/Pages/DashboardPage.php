<?php
declare(strict_types=1);

namespace App\Pages;

use App\Components\PageHeader;
use App\Concerns\ResolvesCurrentUser;
use App\Http\Middleware\SwitchesCurrentTeam;
use App\Models\Team;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Breadcrumb;
use Lattice\Ui\Components\Card;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsPage(
    route: '{current_team}/dashboard',
    name: 'dashboard',
    middleware: ['auth', 'verified', SwitchesCurrentTeam::class],
    can: 'view',
    on: 'current_team',
)]
class DashboardPage extends AppPage
{
    use ResolvesCurrentUser;

    private ?Team $team = null;

    public function title(): string
    {
        return __('dashboard.title');
    }

    /**
     * @return array<int, Breadcrumb>
     */
    public function breadcrumbs(): array
    {
        if (! $this->team instanceof Team) {
            return [];
        }

        return [
            Breadcrumb::make(
                __('dashboard.title'),
                route('dashboard', ['current_team' => $this->team->slug], absolute: false),
            ),
        ];
    }

    public function render(PageSchema $schema, Team $current_team): PageSchema
    {
        $user = $this->currentUser();

        $this->team = $current_team;

        return $schema->schema([
            Stack::make('dashboard-page')
                ->gap(Gap::Large)
                ->width(Width::Large)
                ->schema([
                    PageHeader::make(
                        'dashboard-heading',
                        __('dashboard.heading'),
                        __('dashboard.welcome', ['name' => $user->name, 'team' => $current_team->name]),
                    ),
                    Grid::make('dashboard-overview')
                        ->columns(3)
                        ->schema([
                            Card::make(__('dashboard.cards.server-driven.title'), __('dashboard.cards.server-driven.body')),
                            Card::make(__('dashboard.cards.team-context.title'), __('dashboard.cards.team-context.body')),
                            Card::make(__('dashboard.cards.composable.title'), __('dashboard.cards.composable.body')),
                        ]),
                    Card::make(__('dashboard.cards.next-steps.title'), __('dashboard.cards.next-steps.body')),
                ]),
        ]);
    }
}
