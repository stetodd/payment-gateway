<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Subscription;

class CancelSubscriptionRequest
{
    public bool $cancelAtPeriodEnd = false;

    public function __construct(public readonly string $subscriptionId)
    {
    }

    public function cancelAtPeriodEnd(): void
    {
        $this->cancelAtPeriodEnd = true;
    }
}
