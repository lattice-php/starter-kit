<?php
declare(strict_types=1);

namespace App\Tables\Teams;

use App\Actions\Teams\RemoveMember;
use App\Actions\Teams\UpdateMemberRole;
use App\Enums\TeamRole;
use App\Models\Team;
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

#[AsTable('teams.members', can: 'view', on: 'team')]
class TeamMembersTable extends TableDefinition
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
            StackColumn::make('member')
                ->label(__('teams.members.column'))
                ->schema([
                    Text::make('')->dataKey('text', 'name')->color(ColorName::Default),
                    Text::make('')->dataKey('text', 'email')->size(Size::Sm),
                ]),
            TextColumn::make('role_label')->label(__('common.field.role')),
        ];
    }

    public function actions(array $row): array
    {
        /** @var Team $team */
        $team = $this->contextModel('team');
        $user = auth()->user();

        if (! $user instanceof User || TeamRole::tryFrom((string) ($row['role'] ?? '')) === TeamRole::Owner) {
            return [];
        }

        $context = ['member' => $row['id']];
        $actions = [];

        if ($user->can('updateMember', $team)) {
            $actions[] = Action::use(UpdateMemberRole::class, $context);
        }

        if ($user->can('removeMember', $team)) {
            $actions[] = Action::use(RemoveMember::class, $context);
        }

        if ($actions === []) {
            return [];
        }

        return [
            ActionGroup::make("teams.members.{$row['id']}.actions")
                ->label(__('teams.members.actions-label'))
                ->actions($actions),
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
                $team->members()
                    ->orderByRaw("CASE team_members.role WHEN 'owner' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
                    ->orderByRaw('LOWER(name)')
                    ->get()
                    ->map(fn (User $member): array => [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'role' => $member->pivot->role->value,
                        'role_label' => $member->pivot->role->getLabel(),
                    ]),
            );
        });
    }
}
