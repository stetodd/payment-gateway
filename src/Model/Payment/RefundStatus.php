<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Payment;

/**
 * Where a refund has got to. A card refund is usually Succeeded at once but
 * may sit Pending; Failed arrives later when the card can no longer take the
 * money back (closed, expired), after the vendor has already said yes.
 */
enum RefundStatus: string
{
    case Pending = 'pending';
    case RequiresAction = 'requires_action';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    /** Accepted by the vendor: the money is on its way back or already there. */
    public function isAccepted(): bool
    {
        return $this === self::Succeeded || $this === self::Pending || $this === self::RequiresAction;
    }
}
