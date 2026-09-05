<?php
declare(strict_types=1);

namespace App\Pages;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lattice\Core\Attributes\AsPage;
use Lattice\Http\Page;
use Lattice\Ui\Components\Badge;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Card;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Variant;
use Lattice\Ui\PageSchema;
use Throwable;

#[AsPage(route: '/', name: 'home')]
final class WelcomePage extends Page
{
    private ?Authenticatable $user = null;

    public function title(): string
    {
        return __('welcome.title');
    }

    protected function boot(Request $request): void
    {
        $user = $request->user();

        $this->user = $user instanceof Authenticatable ? $user : null;
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('welcome-page')
                ->gap(Gap::ExtraLarge)
                ->schema([
                    Stack::make('welcome-hero')
                        ->gap(Gap::Large)
                        ->schema([
                            Badge::make(__('welcome.badge')),
                            Heading::make(__('welcome.heading')),
                            Text::make(__('welcome.subtitle')),
                            Stack::make('welcome-actions')
                                ->gap(Gap::Small)
                                ->schema($this->actions()),
                        ]),
                    Grid::make('welcome-capabilities')
                        ->columns(3)
                        ->schema([
                            Card::make(__('welcome.capabilities.forms.title'), __('welcome.capabilities.forms.body')),
                            Card::make(__('welcome.capabilities.tables.title'), __('welcome.capabilities.tables.body')),
                            Card::make(__('welcome.capabilities.pages.title'), __('welcome.capabilities.pages.body')),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<int, Button>
     */
    private function actions(): array
    {
        if ($this->user instanceof Authenticatable) {
            return [
                Button::make(__('welcome.actions.dashboard'))
                    ->href($this->dashboardUrl()),
            ];
        }

        return [
            Button::make(__('common.action.log-in'))
                ->href($this->namedRouteUrl('login'))
                ->variant(Variant::Secondary),
            Button::make(__('welcome.actions.register'))
                ->href($this->namedRouteUrl('register')),
        ];
    }

    private function dashboardUrl(): string
    {
        if (! $this->user instanceof Authenticatable) {
            return $this->namedRouteUrl('home');
        }

        $currentTeam = $this->user->currentTeam ?? null;

        if (is_object($currentTeam) && property_exists($currentTeam, 'slug')) {
            return $this->namedRouteUrl('dashboard', [
                'current_team' => $currentTeam->slug,
            ]);
        }

        return $this->namedRouteUrl('dashboard', fallback: $this->namedRouteUrl('home'));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function namedRouteUrl(string $name, array $parameters = [], string $fallback = '#'): string
    {
        if (! Route::has($name)) {
            return $fallback;
        }

        try {
            return route($name, $parameters);
        } catch (Throwable) {
            return $fallback;
        }
    }
}
