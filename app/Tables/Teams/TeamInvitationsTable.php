<?php
declare(strict_types=1);

namespace App\Tables\Teams;

use App\Actions\Teams\CancelInvitation;
use App\Concerns\ResolvesTeamFromContext;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Actions\Components\ActionGroup;
use Lattice\Lattice\Attributes\AsTable;
use Lattice\Lattice\Tables\CallbackTableSource;
use Lattice\Lattice\Tables\Columns\StackColumn;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Color;
use Lattice\Lattice\Ui\Enums\Size;
use Lattice\Lattice\Tables\Columns\TextColumn;
use Lattice\Lattice\Tables\Contracts\TableSource;
use Lattice\Lattice\Tables\Enums\PaginationType;
use Lattice\Lattice\Tables\TableDefinition;
use Lattice\Lattice\Tables\TableQuery;
use Lattice\Lattice\Tables\TableResult;

#[AsTable('teams.invitations')]
class TeamInvitationsTable extends TableDefinition
{
    use ResolvesTeamFromContext;

    public function layout(): string
    {
        return 'grid';
    }

    public function pagination(): PaginationType
    {
        return PaginationType::None;
    }

    public function columns(): array
    {
        return [
            StackColumn::make('invitation')
                ->label(__('teams.invitations.column'))
                ->schema([
                    Text::make('')->dataKey('text', 'email')->color(Color::Default),
                    Text::make('')->dataKey('text', 'role_label')->size(Size::Sm),
                ]),
            TextColumn::make('created_at')->label(__('teams.invitations.sent-column')),
        ];
    }

    public function actions(array $row): array
    {
        $team = $this->optionalTeamFromContext();
        $user = auth()->user();

        if (! $team instanceof Team || ! $user instanceof User || ! $user->can('cancelInvitation', $team)) {
            return [];
        }

        return [
            ActionGroup::make("teams.invitations.{$row['id']}.actions")
                ->label(__('teams.invitations.actions-label'))
                ->actions([
                    Action::use(CancelInvitation::class, ['team' => $team->slug, 'invitation' => $row['code']]),
                ]),
        ];
    }

    public function source(): TableSource
    {
        return new CallbackTableSource(function (TableQuery $query): TableResult {
            $team = $this->optionalTeamFromContext();
            $user = auth()->user();

            if (! $team instanceof Team || ! $user instanceof User || ! $user->belongsToTeam($team)) {
                return TableResult::fromItems([]);
            }

            return TableResult::fromItems(
                $team->invitations()
                    ->whereNull('accepted_at')
                    ->oldest()
                    ->get()
                    ->map(fn (TeamInvitation $invitation): array => [
                        'id' => $invitation->id,
                        'code' => $invitation->code,
                        'email' => $invitation->email,
                        'role' => $invitation->role->value,
                        'role_label' => $invitation->role->getLabel(),
                        'created_at' => $invitation->created_at?->diffForHumans() ?? '',
                    ]),
            );
        });
    }
}
