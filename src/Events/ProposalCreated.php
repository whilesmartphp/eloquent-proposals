<?php

namespace Whilesmart\Proposals\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Whilesmart\Proposals\Models\Proposal;

class ProposalCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Proposal $proposal) {}
}
