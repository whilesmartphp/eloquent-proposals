<?php

use Whilesmart\Proposals\Models\Proposal;
use Whilesmart\Proposals\ResponseFormatters\DefaultResponseFormatter;

return [
    'model' => Proposal::class,
    'response_formatter' => DefaultResponseFormatter::class,
    'hooks' => [],
    'register_routes' => env('PROPOSALS_REGISTER_ROUTES', true),
    'route_prefix' => env('PROPOSALS_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],
    'proposals_table' => env('PROPOSALS_TABLE', 'proposals'),
    'number_prefix' => env('PROPOSAL_NUMBER_PREFIX', 'PRO-'),
    'number_length' => (int) env('PROPOSAL_NUMBER_LENGTH', 5),
];
