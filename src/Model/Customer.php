<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model;

class Customer
{
    public function __construct(
        public readonly string $id,
        /** @var array<string, string> */
        public readonly array $metadata,
    ) {
    }
}
