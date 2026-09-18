<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model;

use Stetodd\PaymentGateway\Model\Subscription\Status;

class Subscription
{
    public function __construct(
        public readonly string $subscriptionId,
        public Status $status,
        public \DateTimeImmutable $currentPeriodStart,
        public \DateTimeImmutable $currentPeriodEnd,
        public bool $cancelAtPeriodEnd,
        /** When the subscription is scheduled to end, if a moment was named rather than just the period end. */
        public ?\DateTimeImmutable $cancelAt = null,
    ) {
    }
}
