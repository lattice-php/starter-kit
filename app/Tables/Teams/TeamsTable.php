<?php
declare(strict_types=1);

namespace App\Tables\Teams;

use App\Models\Team;
use App\Models\User;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsTable;
use Lattice\Lattice\Core\Enums\ColorName;
use Lattice\Lattice\Tables\CallbackTableSource;
use Lattice\Lattice\Tables\Columns\StackColumn;
use Lattice\Lattice\Tables\Columns\TextColumn;
use Lattice\Lattice\Tables\Contracts\TableSource;
use Lattice\Lattice\Tables\Enums\PaginationType;
use Lattice\Lattice\Tables\TableDefinition;
use Lattice\Lattice\Tables\TableQuery;
use Lattice\Lattice\Tables\TableResult;
use Lattice\Lattice\Ui\Components\Component;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Size;
use Lattice\Lattice\Ui\Enums\Variant;

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

            return TableResult::fromItems(
                $user->teams()->get()->map(fn (Team $team): array => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'slug' => $team->slug,
                    'roleLabel' => $user->teamRole($team)?->getLabel(),
                    'status' => $this->statusFor($team->is_personal, $user->isCurrentTeam($team)),
                ]),
            );
        });
    }

    private function statusFor(bool $isPersonal, ?bool $isCurrent): string
    {
        return collect([
            $isPersonal ? __('teams.status.personal') : null,
            $isCurrent ? __('teams.status.current') : null,
        ])->filter()->implode(' / ');
    }
}
