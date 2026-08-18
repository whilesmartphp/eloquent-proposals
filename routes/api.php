<?php

use Illuminate\Support\Facades\Route;
use Whilesmart\Proposals\Http\Controllers\ProposalController;

Route::apiResource('proposals', ProposalController::class);
Route::post('proposals/{proposal}/send', [ProposalController::class, 'send']);
Route::post('proposals/{proposal}/accept', [ProposalController::class, 'accept']);
Route::post('proposals/{proposal}/share', [ProposalController::class, 'share']);
