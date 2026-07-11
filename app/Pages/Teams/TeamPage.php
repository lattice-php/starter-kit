<?php
declare(strict_types=1);

namespace App\Pages\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Forms\Teams\DeleteTeamForm;
use App\Forms\Teams\InviteTeamMemberForm;
use App\Forms\Teams\UpdateTeamForm;
use App\Models\Team;
use App\Pages\Concerns\ListensForUserNotifications;
use App\Tables\Teams\TeamInvitationsTable;
use App\Tables\Teams\TeamMembersTable;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
use Lattice\Lattice\Ui\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Http\Page;
use Lattice\Lattice\Tables\Components\Table;

#[AsPage(route: 'settings/teams/{team}', name: 'teams.edit', layout: PageLayout::App, container: PageContainer::Default, middleware: ['web', 'auth', 'verified', 'can:view,team'])]
class TeamPage extends Page
{
    use ListensForUserNotifications;
    use ResolvesCurrentUser;

    private ?Team $team = null;

    public function title(): ?string
    {
        return $this->team?->name;
    }

    public function render(PageSchema $schema, Request $request, Team $team): PageSchema
    {
        $user = $this->currentUser();

        $this->team = $team;

        return $schema->schema([
            Stack::make('team-page')
                ->gap(Gap::Large)
                ->width(Width::Medium)
                ->schema([
                    Stack::make('team-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make($team->name, 1),
                            Text::make(__('teams.show.subtitle')),
                        ]),
                    Stack::make('team-details-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make(__('teams.show.details-heading'), 2),
                            Text::make(__('teams.show.details-subtitle')),
                        ])
                        ->visible($user->can('update', $team)),
                    Form::use(UpdateTeamForm::class, ['team' => $team->slug])
                        ->visible($user->can('update', $team)),
                    Stack::make('team-invite-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make(__('teams.show.invite-heading'), 2),
                            Text::make(__('teams.show.invite-subtitle')),
                        ])
                        ->visible($user->can('inviteMember', $team)),
                    Form::use(InviteTeamMemberForm::class, ['team' => $team->slug])
                        ->visible($user->can('inviteMember', $team)),
                    Stack::make('team-members-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make(__('teams.show.members-heading'), 2),
                            Text::make(__('teams.show.members-subtitle')),
                        ]),
                    Table::lazy(TeamMembersTable::class, ['team' => $team->slug]),
                    Stack::make('team-invitations-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make(__('teams.show.invitations-heading'), 2),
                            Text::make(__('teams.show.invitations-subtitle')),
                        ]),
                    Table::lazy(TeamInvitationsTable::class, ['team' => $team->slug]),
                    Form::use(DeleteTeamForm::class, ['team' => $team->slug])
                        ->visible($user->can('delete', $team)),
                ]),
        ]);
    }
}
