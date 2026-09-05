<?php
declare(strict_types=1);

namespace App\Tables\Teams;

use App\Actions\Teams\CancelInvitation;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Lattice\Actions\Components\Action;
use Lattice\Actions\Components\ActionGroup;
use Lattice\Core\Enums\ColorName;
use Lattice\Table\Attributes\AsTable;
use Lattice\Table\CallbackTableSource;
use Lattice\Table\Columns\StackColumn;
use Lattice\Table\Columns\TextColumn;
use Lattice\Table\Contracts\TableSource;
use Lattice\Table\Enums\PaginationType;
use Lattice\Table\TableDefinition;
use Lattice\Table\TableQuery;
use Lattice\Table\TableResult;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Size;

#[AsTable('teams.invitations', can: 'view', on: 'team')]
class TeamInvitationsTable extends TableDefinition
{
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
                    Text::make('')->dataKey('text', 'email')->color(ColorName::Default),
                    Text::make('')->dataKey('text', 'role_label')->size(Size::Sm),
                ]),
            TextColumn::make('created_at')->label(__('teams.invitations.sent-column')),
        ];
    }

    public function actions(array $row): array
    {
        /** @var Team $team */
        $team = $this->contextModel('team');
        $user = auth()->user();

        if (! $user instanceof User || ! $user->can('cancelInvitation', $team)) {
            return [];
        }

        return [
            ActionGroup::make("teams.invitations.{$row['id']}.actions")
                ->label(__('teams.invitations.actions-label'))
                ->actions([
                    Action::use(CancelInvitation::class, ['invitation' => $row['code']]),
                ]),
        ];
    }

    public function source(): TableSource
    {
        return new CallbackTableSource(function (TableQuery $query): TableResult {
            $team = $this->contextModelOrNull('team');

            if (! $team instanceof Team) {
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
