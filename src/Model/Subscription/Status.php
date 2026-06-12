<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Subscription;

enum Status: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';
    case PastDue = 'past_due';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
    case Trialing = 'trialing';
}
