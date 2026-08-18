<?php

namespace Whilesmart\Proposals\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;
use Whilesmart\Proposals\Enums\HookAction;
use Whilesmart\Proposals\Enums\ProposalStatus;
use Whilesmart\Proposals\Events\ProposalAccepted;
use Whilesmart\Proposals\Events\ProposalSent;
use Whilesmart\Proposals\Events\ProposalShared;
use Whilesmart\Proposals\Http\Requests\StoreProposalRequest;
use Whilesmart\Proposals\Http\Requests\UpdateProposalRequest;
use Whilesmart\Proposals\Http\Resources\ProposalResource;
use Whilesmart\Proposals\Models\Proposal;
use Whilesmart\Proposals\Traits\ApiResponse;
use Whilesmart\Proposals\Traits\HasHooks;

class ProposalController extends Controller
{
    use ApiResponse, AuthorizesOwnerController, HasHooks;

    private function model(): string
    {
        return config('proposals.model', Proposal::class);
    }

    public function index(Request $request): JsonResponse
    {
        $request = $this->runBeforeHooks($request, HookAction::Index);

        $query = $this->scopeAccessibleOwners(($this->model())::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(title) like ?', [$term])
                    ->orWhereRaw('lower(number) like ?', [$term]);
            });
        }

        $proposals = $query->orderByDesc('updated_at')
            ->paginate((int) $request->input('per_page', 25));

        $response = $this->success(
            ProposalResource::collection($proposals)->response()->getData(true)
        );

        return $this->runAfterHooks($request, $response, HookAction::Index);
    }

    public function store(StoreProposalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $request = $this->runBeforeHooks($request, HookAction::Store);

        $proposal = ($this->model())::create($data);

        $response = $this->success(new ProposalResource($proposal), 'Proposal created.', 201);

        return $this->runAfterHooks($request, $response, HookAction::Store);
    }

    public function show(Proposal $proposal, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $request = $this->runBeforeHooks($request, HookAction::Show);

        $response = $this->success(new ProposalResource($proposal));

        return $this->runAfterHooks($request, $response, HookAction::Show);
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $data = $request->validated();
        $request = $this->runBeforeHooks($request, HookAction::Update);

        $proposal->update($data);

        $response = $this->success(new ProposalResource($proposal->fresh()), 'Proposal updated.');

        return $this->runAfterHooks($request, $response, HookAction::Update);
    }

    public function destroy(Proposal $proposal, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $request = $this->runBeforeHooks($request, HookAction::Destroy);

        $proposal->delete();

        $response = $this->success(null, 'Proposal deleted.');

        return $this->runAfterHooks($request, $response, HookAction::Destroy);
    }

    public function send(Proposal $proposal, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $request = $this->runBeforeHooks($request, HookAction::Send);

        $proposal->update([
            'status' => ProposalStatus::Sent,
            'sent_at' => now(),
        ]);

        ProposalSent::dispatch($proposal->fresh());

        $response = $this->success(new ProposalResource($proposal->fresh()), 'Proposal sent.');

        return $this->runAfterHooks($request, $response, HookAction::Send);
    }

    public function accept(Proposal $proposal, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $request = $this->runBeforeHooks($request, HookAction::Accept);

        $proposal->update([
            'status' => ProposalStatus::Accepted,
            'accepted_at' => now(),
        ]);

        ProposalAccepted::dispatch($proposal->fresh());

        $response = $this->success(new ProposalResource($proposal->fresh()), 'Proposal accepted.');

        return $this->runAfterHooks($request, $response, HookAction::Accept);
    }

    public function share(Proposal $proposal, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($proposal, $request->user());
        $request = $this->runBeforeHooks($request, HookAction::Share);

        $validated = $request->validate([
            'access' => ['nullable', 'string', 'max:40'],
            'audience' => ['nullable', 'string', 'max:120'],
        ]);

        $share = $proposal->share(
            $validated['access'] ?? 'read',
            $proposal->owner,
            $validated['audience'] ?? null,
        );

        ProposalShared::dispatch($proposal, $share);

        $response = $this->success([
            'token' => $share->token,
            'access' => $share->access,
            'audience' => $share->audience,
        ], 'Proposal shared.', 201);

        return $this->runAfterHooks($request, $response, HookAction::Share);
    }
}
