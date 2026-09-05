<?php
declare(strict_types=1);

namespace App\Components;

use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;

/**
 * The title block a page or one of its sections opens with. A factory rather
 * than a wire component: it composes existing components and adds nothing the
 * client has to know about.
 */
final class PageHeader
{
    public static function make(string $key, string $title, ?string $description = null): Stack
    {
        return self::build($key, $title, $description, 1);
    }

    public static function section(string $key, string $title, ?string $description = null): Stack
    {
        return self::build($key, $title, $description, 2);
    }

    private static function build(string $key, string $title, ?string $description, int $level): Stack
    {
        $schema = [Heading::make($title, $level)];

        if ($description !== null) {
            $schema[] = Text::make($description);
        }

        return Stack::make($key)->gap(Gap::Small)->schema($schema);
    }
}
