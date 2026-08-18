<?php

namespace Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Whilesmart\Proposals\Traits\HasProposals;

class HostWorkspace extends Model
{
    use HasProposals;

    protected $table = 'workspaces';

    protected $guarded = [];
}
