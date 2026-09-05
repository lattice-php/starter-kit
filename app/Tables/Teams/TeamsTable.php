<?php
declare(strict_types=1);

namespace App\Tables\Teams;

use App\Models\Team;
use App\Models\User;
use Lattice\Actions\Components\Action;
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
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Size;
use Lattice\Ui\Enums\Variant;

#[AsTable('teams.index')]
class TeamsTable extends TableDefinition
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
            StackColumn::make('team')
                ->label(__('teams.columns.team'))
                ->schema([
                    Text::make('')->dataKey('text', 'name')->color(ColorName::Default),
                    Text::make('')->dataKey('text', 'roleLabel')->size(Size::Sm),
                ]),
            TextColumn::make('status')->label(__('common.field.status')),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, Component>
     */
    public function actions(array $row): array
    {
        $slug = (string) ($row['slug'] ?? '');

        if ($slug === '') {
            return [];
        }

        return [
            Action::make("teams.{$row['id']}.edit")
                ->endpoint(route('teams.edit', ['team' => $slug], absolute: false))
                ->label(__('teams.actions.edit'))
                ->method(HttpMethod::Get)
                ->variant(Variant::Secondary),
        ];
    }

    public function source(): TableSource
    {
        return new CallbackTableSource(function (TableQuery $query): TableResult {
            $user = auth()->user();

            if (! $user instanceof User) {
                return TableResult::fromItems([]);
            }

            // The membership pivot already carries the role, so reading it off the
            // loaded row keeps this to one query instead of one per team.
            return TableResult::fromItems(
                $user->teams()->get()->map(fn (Team $team): array => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'slug' => $team->slug,
                    'roleLabel' => $team->pivot->role->getLabel(),
                    'status' => $this->statusFor($team->is_personal, $user->isCurrentTeam($team)),
                ]),
            );
        });
    }

    private function statusFor(bool $isPersonal, bool $isCurrent): string
    {
        return collect([
            $isPersonal ? __('teams.status.personal') : null,
            $isCurrent ? __('teams.status.current') : null,
        ])->filter()->implode(' / ');
    }
}
