<?php

namespace Whilesmart\Proposals\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Whilesmart\Proposals\Enums\HookAction;
use Whilesmart\Proposals\Interfaces\ProposalHookInterface;

trait HasHooks
{
    protected function runBeforeHooks(Request $request, HookAction $action): Request
    {
        foreach ($this->hooks() as $hook) {
            $result = $hook->before($request, $action->value);
            if ($result instanceof Request) {
                $request = $result;
            }
        }

        return $request;
    }

    protected function runAfterHooks(Request $request, JsonResponse $response, HookAction $action): JsonResponse
    {
        foreach ($this->hooks() as $hook) {
            $result = $hook->after($request, $response, $action->value);
            if ($result instanceof JsonResponse) {
                $response = $result;
            }
        }

        return $response;
    }

    /** @return ProposalHookInterface[] */
    private function hooks(): array
    {
        return collect(config('proposals.hooks', []))
            ->filter(fn ($class) => class_exists($class))
            ->map(fn ($class) => app($class))
            ->filter(fn ($hook) => $hook instanceof ProposalHookInterface)
            ->all();
    }
}
