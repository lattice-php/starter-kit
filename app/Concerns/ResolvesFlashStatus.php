<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Http\Request;

trait ResolvesFlashStatus
{
    protected function flashStatus(Request $request): ?string
    {
        $status = $request->session()->get('status');

        return is_string($status) ? $status : null;
    }
}
