<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Laravel\Fortify\Features;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Services\ComponentReferenceSigner;
use Lattice\Lattice\Support\Testing\InteractsWithLatticeComponents;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithLatticeComponents;

    /**
     * Call an action endpoint with a directly sealed ref. Lattice refuses to
     * serialize (and therefore seal) an action its definition denies, so denial
     * tests forge the ref the way a stale or tampered client would and prove
     * the endpoint still rejects it.
     *
     * @param  class-string  $action
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $context
     * @return TestResponse<\Illuminate\Http\JsonResponse>
     */
    protected function callActionForged(string $action, array $data = [], array $context = []): TestResponse
    {
        $key = (new ReflectionClass($action))->getAttributes(AsAction::class)[0]->newInstance()->key;
        $ref = app(ComponentReferenceSigner::class)->seal('action', $key, $context);
        $endpoint = str_replace('{action}', $key, (string) config('lattice.actions.endpoint'));

        return $this->postJson('/'.$endpoint, $data, ['X-Lattice-Ref' => $ref]);
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
