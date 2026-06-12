<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Subscription;

class UpdateSubscriptionPlanRequest
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $newPriceId,
    ) {
    }
}
