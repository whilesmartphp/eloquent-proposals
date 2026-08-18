<?php

namespace Whilesmart\Proposals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Customers\Models\Customer;
use Whilesmart\Proposals\Database\Factories\ProposalFactory;
use Whilesmart\Proposals\Enums\ProposalStatus;
use Whilesmart\Shareables\Traits\Shareable;

class Proposal extends Model
{
    use HasFactory, Shareable, SoftDeletes;

    /** Suggested section block types for a proposal document. */
    public const SECTION_TYPES = ['cover', 'summary', 'scope', 'timeline', 'terms', 'pricing'];

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ProposalStatus::class,
        'sections' => 'array',
        'sent_at' => 'date',
        'accepted_at' => 'date',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Proposal $proposal) {
            if (empty($proposal->status)) {
                $proposal->status = ProposalStatus::Draft;
            }
            if (empty($proposal->number)) {
                $proposal->number = static::generateNumber($proposal);
            }
        });
    }

    /**
     * Next per-owner proposal number, e.g. PRO-00001. The (owner, number)
     * unique index is the final guard against duplicates.
     */
    public static function generateNumber(Proposal $proposal): string
    {
        $prefix = (string) config('proposals.number_prefix', 'PRO-');
        $length = (int) config('proposals.number_length', 5);

        $last = static::withTrashed()
            ->where('owner_type', $proposal->owner_type)
            ->where('owner_id', $proposal->owner_id)
            ->where('number', 'like', $prefix.'%')
            ->orderByRaw('LENGTH(number) DESC')
            ->orderBy('number', 'DESC')
            ->value('number');

        $seq = $last ? (int) str_replace($prefix, '', $last) : 0;

        return $prefix.str_pad((string) ($seq + 1), $length, '0', STR_PAD_LEFT);
    }

    public function getTable(): string
    {
        return config('proposals.proposals_table', 'proposals');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function newFactory(): ProposalFactory
    {
        return ProposalFactory::new();
    }
}
