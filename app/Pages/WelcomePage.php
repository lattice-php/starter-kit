<?php
declare(strict_types=1);

namespace App\Pages;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Badge;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Card;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Http\Page;
use Throwable;

#[AsPage(route: '/', name: 'home', middleware: ['web'])]
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
                ->variant(ButtonVariant::Secondary),
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
