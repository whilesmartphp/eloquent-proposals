<?php

namespace Tests\Feature;

use Tests\Support\HostWorkspace;
use Tests\TestCase;
use Whilesmart\Proposals\Enums\ProposalStatus;

class ProposalTest extends TestCase
{
    public function test_it_numbers_a_proposal_and_keeps_its_sections_and_estimate(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $proposal = $ws->proposals()->create([
            'title' => 'Website revamp',
            'estimate_id' => 123,
            'sections' => [
                ['type' => 'summary', 'title' => 'Summary', 'body' => 'What we will do'],
                ['type' => 'pricing', 'title' => 'Pricing', 'body' => 'What it costs'],
            ],
        ]);

        $this->assertSame('PRO-00001', $proposal->number);
        $this->assertSame(ProposalStatus::Draft, $proposal->status);
        $this->assertCount(2, $proposal->sections);
        $this->assertSame('pricing', $proposal->sections[1]['type']);
        $this->assertSame(123, $proposal->estimate_id);
    }

    public function test_it_can_be_shared_and_validated_by_token(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $proposal = $ws->proposals()->create(['title' => 'Retainer']);

        $share = $proposal->share('read', $ws);

        $this->assertNotEmpty($share->token);
        $this->assertTrue($proposal->isSharedViaToken($share->token));
        $this->assertFalse($proposal->isSharedViaToken('not-a-real-token'));
    }
}
