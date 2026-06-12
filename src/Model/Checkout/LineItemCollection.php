<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

class LineItemCollection
{
    /** @var LineItem[] */
    private array $items = [];

    public function add(LineItem $lineItem): void
    {
        $this->items[] = $lineItem;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
