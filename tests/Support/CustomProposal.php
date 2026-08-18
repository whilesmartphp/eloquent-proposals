<?php

namespace Tests\Support;

use Whilesmart\Proposals\Models\Proposal;

// A host model standing in for an app that extends the package Proposal to add
// its own traits/behaviour. Here it stamps where the record was created.
class CustomProposal extends Proposal
{
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Proposal $proposal) {
            $proposal->metadata = array_merge($proposal->metadata ?? [], ['via' => 'custom']);
        });
    }
}
