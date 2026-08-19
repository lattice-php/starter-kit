<?php
declare(strict_types=1);

namespace App\Pages;

use App\Concerns\ResolvesCurrentUser;
use App\Http\Middleware\SwitchesCurrentTeam;
use App\Models\Team;
use App\Pages\Concerns\ListensForUserNotifications;
use Illuminate\Http\Request;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Breadcrumb;
use Lattice\Core\Enums\PageLayout;
use Lattice\Http\Page;
use Lattice\Ui\Components\Card;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsPage(route: '{current_team}/dashboard', name: 'dashboard', layout: PageLayout::App, middleware: ['web', 'auth', 'verified', 'can:view,current_team', SwitchesCurrentTeam::class])]
class DashboardPage extends Page
{
    use ListensForUserNotifications;
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
                            Heading::make(__('dashboard.heading'), 1),
                            Text::make(__('dashboard.welcome', ['name' => $user->name, 'team' => $current_team->name])),
                        ]),
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
