<?php
declare(strict_types=1);

namespace App\Tables\Settings;

use App\Actions\Settings\DeletePasskey;
use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Passkeys\Passkey;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsTable;
use Lattice\Lattice\Tables\CallbackTableSource;
use Lattice\Lattice\Tables\Columns\StackColumn;
use Lattice\Lattice\Tables\Columns\TextColumn;
use Lattice\Lattice\Tables\Contracts\TableSource;
use Lattice\Lattice\Tables\Enums\PaginationType;
use Lattice\Lattice\Tables\TableDefinition;
use Lattice\Lattice\Tables\TableQuery;
use Lattice\Lattice\Tables\TableResult;

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
                ->label('Passkey')
                ->columns([
                    TextColumn::make('name')->label('Name'),
                    TextColumn::make('authenticator')->label('Authenticator'),
                    TextColumn::make('created_at_diff')->label('Created'),
                ]),
            TextColumn::make('last_used_at_diff')->label('Last used'),
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
                        'created_at_diff' => 'Added '.$passkey->created_at->diffForHumans(),
                        'last_used_at_diff' => $passkey->last_used_at === null
                            ? 'Never used'
                            : 'Last used '.$passkey->last_used_at->diffForHumans(),
                    ]),
            );
        });
    }
}
