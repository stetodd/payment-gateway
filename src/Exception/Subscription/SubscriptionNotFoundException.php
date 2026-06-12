<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Exception\Subscription;

class SubscriptionNotFoundException extends \RuntimeException
{
    public function __construct(string $subscriptionId, ?\Throwable $previous = null)
    {
        parent::__construct(message: "Subscription with ID {$subscriptionId} not found", previous: $previous);
    }
}
