<?php
declare(strict_types=1);

namespace App\Tables\Settings;

use App\Actions\Settings\DeletePasskey;
use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Passkeys\Passkey;
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
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Size;

#[AsTable('settings.passkeys')]
class PasskeysTable extends TableDefinition
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
            StackColumn::make('passkey')
                ->label(__('settings.passkeys.column'))
                ->schema([
                    Text::make('')->dataKey('text', 'name')->color(ColorName::Default),
                    Text::make('')->dataKey('text', 'authenticator')->size(Size::Sm),
                    Text::make('')->dataKey('text', 'created_at_diff')->size(Size::Sm),
                ]),
            TextColumn::make('last_used_at_diff')->label(__('settings.passkeys.last-used')),
        ];
    }

    public function actions(array $row): array
    {
        return [
            Action::use(DeletePasskey::class, ['passkey' => $row['id']]),
        ];
    }

    public function source(): TableSource
    {
        return new CallbackTableSource(function (TableQuery $query): TableResult {
            $user = auth()->user();

            if (! $user instanceof User || ! Features::canManagePasskeys()) {
                return TableResult::fromItems([]);
            }

            return TableResult::fromItems(
                $user
                    ->passkeys()
                    ->select(['id', 'name', 'credential', 'created_at', 'last_used_at'])
                    ->latest()
                    ->get()
                    ->map(fn (Passkey $passkey) => [
                        'id' => $passkey->id,
                        'name' => $passkey->name,
                        'authenticator' => $passkey->authenticator ?? '',
                        'created_at_diff' => __('settings.passkeys.added', ['time' => $passkey->created_at?->diffForHumans() ?? '']),
                        'last_used_at_diff' => $passkey->last_used_at === null
                            ? __('settings.passkeys.never-used')
                            : __('settings.passkeys.last-used-at', ['time' => $passkey->last_used_at->diffForHumans()]),
                    ]),
            );
        });
    }
}
