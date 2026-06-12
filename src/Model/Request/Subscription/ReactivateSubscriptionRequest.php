<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Subscription;

class ReactivateSubscriptionRequest
{
    public function __construct(public readonly string $subscriptionId)
    {
    }
}
