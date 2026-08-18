<?php

namespace Whilesmart\Proposals\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Declined = 'declined';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
