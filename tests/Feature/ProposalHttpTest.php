<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Tests\Support\CustomProposal;
use Tests\Support\HostWorkspace;
use Tests\TestCase;
use Whilesmart\Proposals\Events\ProposalSent;

class ProposalHttpTest extends TestCase
{
    public function test_it_creates_numbers_lists_sends_and_shares_over_http(): void
    {
        Event::fake([ProposalSent::class]);
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $create = $this->postJson('/api/proposals', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'title' => 'Website revamp',
            'sections' => [['type' => 'summary', 'title' => 'Summary', 'body' => 'x']],
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.number', 'PRO-00001')
            ->assertJsonPath('data.status', 'draft');

        $id = $create->json('data.id');

        $this->getJson('/api/proposals')
            ->assertOk()
            ->assertJsonPath('data.data.0.id', $id);

        $this->postJson("/api/proposals/{$id}/send")
            ->assertOk()
            ->assertJsonPath('data.status', 'sent');
        Event::assertDispatched(ProposalSent::class);

        $share = $this->postJson("/api/proposals/{$id}/share", ['access' => 'read']);
        $share->assertCreated();
        $this->assertNotEmpty($share->json('data.token'));
    }

    public function test_it_resolves_a_host_model_from_config(): void
    {
        config()->set('proposals.model', CustomProposal::class);
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $this->postJson('/api/proposals', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'title' => 'Custom',
        ])->assertCreated()
            ->assertJsonPath('data.metadata.via', 'custom');
    }
}
