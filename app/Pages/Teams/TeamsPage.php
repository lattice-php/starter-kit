<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Forms\Teams\CreateTeamForm;
use App\Pages\Concerns\ListensForUserNotifications;
use App\Tables\Teams\TeamsTable;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
use Lattice\Lattice\Ui\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Http\Page;
use Lattice\Lattice\Tables\Components\Table;

#[AsPage(route: 'settings/teams', name: 'teams.index', layout: PageLayout::App, container: PageContainer::Default, middleware: ['web', 'auth', 'verified'])]
class TeamsPage extends Page
{
    use ListensForUserNotifications;

    public function title(): string
    {
        return __('teams.index.title');
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('teams-page')
                ->gap(Gap::Large)
                ->width(Width::Large)
                ->schema([
                    Stack::make('teams-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make(__('teams.index.heading'), 1),
                            Text::make(__('teams.index.subtitle')),
                        ]),
                    Form::use(CreateTeamForm::class),
                    Table::use(TeamsTable::class),
                ]),
        ]);
    }
}
