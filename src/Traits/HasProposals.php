<?php

namespace Whilesmart\Proposals\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Proposals\Models\Proposal;

trait HasProposals
{
    public function proposals(): MorphMany
    {
        return $this->morphMany(Proposal::class, 'owner');
    }
}
