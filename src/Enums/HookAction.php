<?php

namespace Whilesmart\Proposals\Enums;

enum HookAction: string
{
    case Index = 'index';
    case Store = 'store';
    case Show = 'show';
    case Update = 'update';
    case Destroy = 'destroy';
    case Send = 'send';
    case Accept = 'accept';
    case Share = 'share';

    public static function values(): array
    {
        return array_map(fn (self $a) => $a->value, self::cases());
    }
}
