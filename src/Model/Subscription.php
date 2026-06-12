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
    ) {
    }
}
