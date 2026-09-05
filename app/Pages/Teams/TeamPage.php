<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Components\PageHeader;
use App\Forms\Teams\DeleteTeamForm;
use App\Forms\Teams\InviteTeamMemberForm;
use App\Forms\Teams\UpdateTeamForm;
use App\Models\Team;
use App\Pages\AppPage;
use App\Tables\Teams\TeamInvitationsTable;
use App\Tables\Teams\TeamMembersTable;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Breadcrumb;
use Lattice\Form\Components\Form;
use Lattice\Table\Components\Table;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsPage(route: 'settings/teams/{team}', name: 'teams.edit', can: 'view', on: 'team')]
class TeamPage extends AppPage
{
    private ?Team $team = null;

    public function title(): ?string
    {
        return $this->team?->name;
    }

    /**
     * @return array<int, Breadcrumb>
     */
    public function breadcrumbs(): array
    {
        $trail = [Breadcrumb::make(__('teams.index.title'), route('teams.index', absolute: false))];

        if ($this->team instanceof Team) {
            $trail[] = Breadcrumb::make(
                $this->team->name,
                route('teams.edit', ['team' => $this->team->slug], absolute: false),
            );
        }

        return $trail;
    }

    public function render(PageSchema $schema, Team $team): PageSchema
    {
        $this->team = $team;

        return $schema->schema([
            Stack::make('team-page')
                ->gap(Gap::Large)
                ->width(Width::Medium)
                ->schema([
                    PageHeader::make('team-heading', $team->name, __('teams.show.subtitle')),
                    PageHeader::section('team-details-heading', __('teams.show.details-heading'), __('teams.show.details-subtitle'))
                        ->can('update', on: 'team'),
                    Form::use(UpdateTeamForm::class),
                    PageHeader::section('team-invite-heading', __('teams.show.invite-heading'), __('teams.show.invite-subtitle'))
                        ->can('inviteMember', on: 'team'),
                    Form::use(InviteTeamMemberForm::class),
                    PageHeader::section('team-members-heading', __('teams.show.members-heading'), __('teams.show.members-subtitle')),
                    Table::lazy(TeamMembersTable::class),
                    PageHeader::section('team-invitations-heading', __('teams.show.invitations-heading'), __('teams.show.invitations-subtitle')),
                    Table::lazy(TeamInvitationsTable::class),
                    Form::use(DeleteTeamForm::class),
                ]),
        ]);
    }
}
