<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Checkout;

use Stetodd\PaymentGateway\Model\Checkout\LineItemCollection;
use Stetodd\PaymentGateway\Model\Customer;

class CreateCheckoutSessionRequest
{
    public function __construct(
        public readonly Customer $customer,
        public readonly LineItemCollection $lineItems,
        /** @var array<string, string> */
        public readonly array $metadata,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return array_merge(
            $this->metadata,
            $this->customer->metadata
        );
    }
}
