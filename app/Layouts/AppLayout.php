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
use Lattice\Core\Attributes\AsLayout;
use Lattice\Core\Enums\ColorName;
use Lattice\Core\Support\Affix;
use Lattice\Layouts\Components\Outlet;
use Lattice\Layouts\LayoutDefinition;
use Lattice\Ui\Components\Avatar;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Dropdown;
use Lattice\Ui\Components\Icon as IconComponent;
use Lattice\Ui\Components\Menu;
use Lattice\Ui\Components\MenuItem;
use Lattice\Ui\Components\Sidebar;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Align;
use Lattice\Ui\Enums\AvatarShape;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Height;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Icon;
use Lattice\Ui\Enums\Justify;
use Lattice\Ui\Enums\Orientation;
use Lattice\Ui\Enums\Placement;
use Lattice\Ui\Enums\Size;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

#[AsLayout('app')]
class AppLayout extends LayoutDefinition
{
    public function schema(PageSchema $schema, Request $request): PageSchema
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $schema->schema([
            Stack::make('app-shell')
                ->direction(Orientation::Horizontal)
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
                    ->direction(Orientation::Horizontal)
                    ->align(Align::Center)
                    ->gap(Gap::Medium)
                    ->schema([
                        Avatar::make(key: 'user-menu-avatar')->name($user->name)->shape(AvatarShape::Rounded),
                        Stack::make()
                            ->width(Width::Fill)
                            ->gap(Gap::None)
                            ->schema([
                                Text::make($user->name)
                                    ->size(Size::Sm)
                                    ->color(ColorName::Default)
                                    ->hideWhenCollapsed(),
                                Text::make($user->email)
                                    ->size(Size::Xs)
                                    ->color(ColorName::Muted)
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

    /**
     * @return array<int, Component>
     */
    private function dropdownTrigger(string $icon, string $label): array
    {
        return [
            Stack::make()
                ->direction(Orientation::Horizontal)
                ->align(Align::Center)
                ->gap(Gap::Small)
                ->schema([
                    IconComponent::make($icon),
                    Text::make($label)
                        ->size(Size::Sm)
                        ->color(ColorName::Default)
                        ->hideWhenCollapsed(),
                ]),
        ];
    }
}
