<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

class LineItem
{
    private function __construct(
        public readonly int $quantity,
        public readonly ?string $priceId,
        public readonly ?int $price,
    ) {
        if ($priceId === null && $price === null) {
            throw new \InvalidArgumentException('Either priceId or price must be provided.');
        }

        if ($priceId !== null && $price !== null) {
            throw new \InvalidArgumentException('Only one of priceId or price can be provided.');
        }

        if ($priceId !== null && $priceId === '') {
            throw new \InvalidArgumentException('PriceId cannot be empty.');
        }

        if ($price !== null && $price <= 0) {
            throw new \InvalidArgumentException('Price must be greater than or equal to 0.');
        }
    }

    public static function fromPriceId(string $priceId, int $quantity): self
    {
        return new self($quantity, $priceId, null);
    }

    public static function fromPrice(int $price, int $quantity): self
    {
        return new self($quantity, null, $price);
    }

    public function getPriceId(): string
    {
        if ($this->priceId === null) {
            throw new \RuntimeException('PriceId is not set.');
        }

        return $this->priceId;
    }

    public function getPrice(): int
    {
        if ($this->price === null) {
            throw new \RuntimeException('Price is not set.');
        }

        return $this->price;
    }
}
