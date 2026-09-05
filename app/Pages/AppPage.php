<?php
declare(strict_types=1);

namespace App\Pages;

use App\Pages\Concerns\ListensForUserNotifications;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Http\Page;

/**
 * The signed-in shell. Layout and middleware are inherited by every concrete
 * page, which then only declares its own route, name, and gate.
 */
#[AsPage(layout: PageLayout::App, middleware: ['auth', 'verified'])]
abstract class AppPage extends Page
{
    use ListensForUserNotifications;
}
