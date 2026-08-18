<?php

namespace Whilesmart\Proposals\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ProposalHookInterface
{
    public function before(Request $request, string $action): ?Request;

    public function after(Request $request, JsonResponse $response, string $action): JsonResponse;
}
