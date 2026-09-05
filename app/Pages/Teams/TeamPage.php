<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Forms\Teams\DeleteTeamForm;
use App\Forms\Teams\InviteTeamMemberForm;
use App\Forms\Teams\UpdateTeamForm;
use App\Models\Team;
use App\Pages\Concerns\ListensForUserNotifications;
use App\Tables\Teams\TeamInvitationsTable;
use App\Tables\Teams\TeamMembersTable;
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

#[AsPage(route: 'settings/teams/{team}', name: 'teams.edit', layout: PageLayout::App, middleware: ['auth', 'verified'], can: 'view', on: 'team')]
class TeamPage extends Page
{
    use ListensForUserNotifications;

    private ?Team $team = null;

    public function title(): ?string
    {
        return $this->team?->name;
    }

    public function render(PageSchema $schema, Request $request, Team $team): PageSchema
    {
        $this->team = $team;

        return $schema->schema([
            Stack::make('team-page')
                ->gap(Gap::Large)
                ->width(Width::Medium)
                ->schema([
                    $this->section('team-heading', $team->name, __('teams.show.subtitle'), 1),
                    $this->section('team-details-heading', __('teams.show.details-heading'), __('teams.show.details-subtitle'))
                        ->can('update', on: 'team'),
                    Form::use(UpdateTeamForm::class),
                    $this->section('team-invite-heading', __('teams.show.invite-heading'), __('teams.show.invite-subtitle'))
                        ->can('inviteMember', on: 'team'),
                    Form::use(InviteTeamMemberForm::class),
                    $this->section('team-members-heading', __('teams.show.members-heading'), __('teams.show.members-subtitle')),
                    Table::lazy(TeamMembersTable::class),
                    $this->section('team-invitations-heading', __('teams.show.invitations-heading'), __('teams.show.invitations-subtitle')),
                    Table::lazy(TeamInvitationsTable::class),
                    Form::use(DeleteTeamForm::class),
                ]),
        ]);
    }

    private function section(string $key, string $heading, string $subtitle, int $level = 2): Stack
    {
        return Stack::make($key)
            ->gap(Gap::Small)
            ->schema([
                Heading::make($heading, $level),
                Text::make($subtitle),
            ]);
    }
}
