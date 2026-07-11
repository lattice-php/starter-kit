<?php
declare(strict_types=1);

namespace App\Layouts;

use App\Actions\SetLocaleAction;
use App\Actions\Teams\SwitchTeam;
use App\Models\Team;
use App\Models\User;
use App\Pages\DashboardPage;
use App\Pages\SettingsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lattice\Lattice\Attributes\AsLayout;
use Lattice\Lattice\Ui\Components\Icon as IconComponent;
use Lattice\Lattice\Ui\Components\RawBlock;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Color;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\Height;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Icon;
use Lattice\Lattice\Ui\Enums\Justify;
use Lattice\Lattice\Ui\Enums\Placement;
use Lattice\Lattice\Ui\Enums\Size;
use Lattice\Lattice\Ui\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Layouts\Components\Dropdown;
use Lattice\Lattice\Layouts\Components\Menu;
use Lattice\Lattice\Layouts\Components\MenuItem;
use Lattice\Lattice\Layouts\Components\Outlet;
use Lattice\Lattice\Layouts\Components\Sidebar;
use Lattice\Lattice\Layouts\LayoutDefinition;
use Lattice\Lattice\Support\Affix;

#[AsLayout('app')]
class AppLayout extends LayoutDefinition
{
    public function schema(PageSchema $schema, Request $request): PageSchema
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $schema->schema([
            Stack::make('app-shell')
                ->direction('row')
                ->height(Height::Screen)
                ->schema([
                    Sidebar::make('app-sidebar')->collapsible()->items([
                        Stack::make('sidebar-body')
                            ->width(Width::Fill)
                            ->justify(Justify::Between)
                            ->schema([
                                Stack::make('sidebar-top')->schema([
                                    $this->teamSwitcher($user),
                                    Menu::make('sidebar')->items([
                                        MenuItem::fromPage(DashboardPage::class)->label(__('navigation.dashboard'))->prefix(Icon::LayoutDashboard),
                                    ]),
                                ]),
                                $this->userMenu($user),
                            ]),
                    ]),
                    Stack::make('app-main')
                        ->width(Width::Fill)
                        ->schema([
                            Outlet::make(),
                        ]),
                ]),
        ]);
    }

    private function teamSwitcher(User $user): Dropdown
    {
        $currentTeam = $user->currentTeam;

        $items = $user->teams
            ->map(function (Team $team) use ($currentTeam): MenuItem {
                $item = MenuItem::make($team->name)
                    ->action(SwitchTeam::class, ['team' => $team->slug]);

                return $team->is($currentTeam) ? $item->suffix(Icon::Check) : $item;
            })
            ->all();

        $items[] = MenuItem::make(__('navigation.team-settings'))
            ->href(route('teams.index', absolute: false))
            ->prefix(Icon::Settings);

        return Dropdown::make('team-switcher')
            ->placement(Placement::Right)
            ->trigger($this->dropdownTrigger('users', $currentTeam->name ?? __('navigation.select-team')))
            ->items($items);
    }

    private function userMenu(User $user): Dropdown
    {
        return Dropdown::make('user-menu')
            ->placement(Placement::Top)
            ->trigger([
                Stack::make()
                    ->direction('row')
                    ->align(Align::Center)
                    ->gap(Gap::Medium)
                    ->schema([
                        RawBlock::make('user-menu-avatar')->html($this->avatarSvg($user)),
                        Stack::make()
                            ->width(Width::Fill)
                            ->gap(Gap::None)
                            ->schema([
                                Text::make($user->name)
                                    ->size(Size::Sm)
                                    ->color(Color::Default)
                                    ->hideWhenCollapsed(),
                                Text::make($user->email)
                                    ->size(Size::Xs)
                                    ->color(Color::Muted)
                                    ->hideWhenCollapsed(),
                            ]),
                    ]),
            ])
            ->items([
                MenuItem::fromPage(SettingsPage::class)->label(__('navigation.settings'))->prefix(Icon::Settings),
                ...$this->languageMenuItems(),
                MenuItem::make(__('common.action.log-out'))
                    ->href(route('logout', absolute: false))
                    ->prefix(Affix::icon('log-out'))
                    ->method(HttpMethod::Post),
            ]);
    }

    /**
     * @return array<int, MenuItem>
     */
    private function languageMenuItems(): array
    {
        $current = app()->getLocale();
        $configured = config('lattice.i18n.locales', []);
        $locales = is_array($configured)
            ? array_values(array_filter($configured, is_string(...)))
            : [];

        return array_map(function (string $locale) use ($current): MenuItem {
            $item = MenuItem::make(__('language.'.$locale))
                ->action(SetLocaleAction::class, ['locale' => $locale]);

            return $locale === $current ? $item->suffix(Icon::Check) : $item;
        }, $locales);
    }

    private function dropdownTrigger(string $icon, string $label): array
    {
        return [
            Stack::make()
                ->direction('row')
                ->align(Align::Center)
                ->gap(Gap::Small)
                ->schema([
                    IconComponent::make($icon),
                    Text::make($label)
                        ->size(Size::Sm)
                        ->color(Color::Default)
                        ->hideWhenCollapsed(),
                ]),
        ];
    }

    private function avatarSvg(User $user): string
    {
        $name = $user->name;
        $initials = e($this->initials($name));
        $label = e($name);

        return <<<HTML
<svg class="size-8 shrink-0 rounded-md bg-lt-muted text-lt-fg" viewBox="0 0 32 32" role="img" aria-label="{$label}">
    <rect width="32" height="32" rx="6" fill="currentColor" opacity=".08"/>
    <text x="16" y="20" text-anchor="middle" class="fill-current text-xs font-medium">{$initials}</text>
</svg>
HTML;
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::of($part)->substr(0, 1)->upper()->toString())
            ->implode('');
    }
}
