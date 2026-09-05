<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Components\PageHeader;
use App\Forms\Teams\CreateTeamForm;
use App\Pages\AppPage;
use App\Tables\Teams\TeamsTable;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Breadcrumb;
use Lattice\Form\Components\Form;
use Lattice\Table\Components\Table;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsPage(route: 'settings/teams', name: 'teams.index')]
class TeamsPage extends AppPage
{
    public function title(): string
    {
        return __('teams.index.title');
    }

    /**
     * @return array<int, Breadcrumb>
     */
    public function breadcrumbs(): array
    {
        return [
            Breadcrumb::make(__('teams.index.title'), route('teams.index', absolute: false)),
        ];
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('teams-page')
                ->gap(Gap::Large)
                ->width(Width::Large)
                ->schema([
                    PageHeader::make('teams-heading', __('teams.index.heading'), __('teams.index.subtitle')),
                    Form::use(CreateTeamForm::class),
                    Table::use(TeamsTable::class),
                ]),
        ]);
    }
}
