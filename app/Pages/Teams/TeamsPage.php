<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Forms\Teams\CreateTeamForm;
use App\Pages\Concerns\ListensForUserNotifications;
use App\Tables\Teams\TeamsTable;
use Illuminate\Http\Request;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Form;
use Lattice\Http\Page;
use Lattice\Table\Components\Table;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsPage(route: 'settings/teams', name: 'teams.index', layout: PageLayout::App, middleware: ['web', 'auth', 'verified'])]
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
