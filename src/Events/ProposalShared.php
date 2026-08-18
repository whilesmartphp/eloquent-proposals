<?php

namespace Whilesmart\Proposals\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Whilesmart\Proposals\Models\Proposal;
use Whilesmart\Shareables\Models\Share;

class ProposalShared
{
    use Dispatchable, SerializesModels;

    public function __construct(public Proposal $proposal, public Share $share) {}
}
