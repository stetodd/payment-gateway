<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Subscription;

/**
 * One paid invoice of a subscription: the payment to refund against, what was
 * paid, and the service period the payment bought.
 */
class SubscriptionPayment
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $invoiceId,
        /** Pass to refundPayment(). */
        public readonly string $paymentId,
        public readonly int $amountPaid,
        public readonly string $currency,
        public readonly \DateTimeImmutable $paidAt,
        public readonly \DateTimeImmutable $periodStart,
        public readonly \DateTimeImmutable $periodEnd,
    ) {
    }
}
