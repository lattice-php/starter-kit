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
        return __('dashboard.title');
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
                'title' => __('dashboard.title'),
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
