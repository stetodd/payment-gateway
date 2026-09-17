<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

/**
 * Short messages a hosted checkout shows alongside its own copy: beside the
 * pay button, and after the customer has paid.
 */
class CustomText
{
    public function __construct(
        public readonly ?string $submit = null,
        public readonly ?string $afterSubmit = null,
    ) {
    }
}
