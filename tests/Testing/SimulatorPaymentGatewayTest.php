<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Tests\Testing;

use PHPUnit\Framework\TestCase;
use Stetodd\PaymentGateway\Exception\Payment\PaymentNotFoundException;
use Stetodd\PaymentGateway\Exception\Payment\RefundFailedException;
use Stetodd\PaymentGateway\Model\Checkout\CustomText;
use Stetodd\PaymentGateway\Model\Checkout\LineItem;
use Stetodd\PaymentGateway\Model\Checkout\LineItemCollection;
use Stetodd\PaymentGateway\Model\Checkout\Session;
use Stetodd\PaymentGateway\Model\Customer;
use Stetodd\PaymentGateway\Model\Payment\RefundStatus;
use Stetodd\PaymentGateway\Model\Request\Checkout\CreateCheckoutSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\RefundPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\GetSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Subscription\SubscriptionPayment;
use Stetodd\PaymentGateway\Testing\SimulatorPaymentGateway;

final class SimulatorPaymentGatewayTest extends TestCase
{
    private SimulatorPaymentGateway $gateway;

    protected function setUp(): void
    {
        $this->gateway = new SimulatorPaymentGateway();
    }

    public function test_the_latest_subscription_payment_is_the_most_recently_paid(): void
    {
        $this->gateway->recordSubscriptionPayment($this->payment('pi_first', '2026-09-01'));
        $this->gateway->recordSubscriptionPayment($this->payment('pi_second', '2026-10-01'));

        $latest = $this->gateway->findLatestSubscriptionPayment(new GetSubscriptionRequest('sub_1'));

        self::assertSame('pi_second', $latest?->paymentId);
    }

    public function test_a_subscription_with_nothing_paid_has_no_latest_payment(): void
    {
        self::assertNull($this->gateway->findLatestSubscriptionPayment(new GetSubscriptionRequest('sub_1')));
    }

    public function test_a_full_refund_returns_what_was_paid(): void
    {
        $this->gateway->recordSubscriptionPayment($this->payment('pi_1', '2026-09-01', 1500));

        $refund = $this->gateway->refundPayment(new RefundPaymentRequest('pi_1'));

        self::assertSame(RefundStatus::Succeeded, $refund->status);
        self::assertSame(1500, $refund->amount);
        self::assertSame(1500, $this->gateway->refundedAmount('pi_1'));
    }

    public function test_partial_refunds_add_up_to_the_amount_paid_and_no_further(): void
    {
        $this->gateway->recordSubscriptionPayment($this->payment('pi_1', '2026-09-01', 1500));

        $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 1000));
        $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 500));

        $this->expectException(RefundFailedException::class);
        $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 1));
    }

    public function test_a_refund_larger_than_the_payment_is_refused(): void
    {
        $this->gateway->recordSubscriptionPayment($this->payment('pi_1', '2026-09-01', 1500));

        $this->expectException(RefundFailedException::class);
        $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 1501));
    }

    public function test_an_uncaptured_hold_cannot_be_refunded(): void
    {
        $this->gateway->holdPayment('pi_hold', 1800);

        $this->expectException(RefundFailedException::class);
        $this->gateway->refundPayment(new RefundPaymentRequest('pi_hold'));
    }

    public function test_a_forced_failure_refuses_the_next_refund_only(): void
    {
        $this->gateway->recordSubscriptionPayment($this->payment('pi_1', '2026-09-01', 1500));
        $this->gateway->failNextRefund('expired_or_canceled_card');

        try {
            $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 100));
            self::fail('The first refund should have been refused.');
        } catch (RefundFailedException $e) {
            self::assertStringContainsString('expired_or_canceled_card', $e->getMessage());
        }

        self::assertSame(0, $this->gateway->refundedAmount('pi_1'));
        self::assertSame(100, $this->gateway->refundPayment(new RefundPaymentRequest('pi_1', 100))->amount);
    }

    public function test_refunding_an_unknown_payment_is_not_found(): void
    {
        $this->expectException(PaymentNotFoundException::class);
        $this->gateway->refundPayment(new RefundPaymentRequest('pi_missing'));
    }

    public function test_a_refund_amount_below_one_is_rejected_by_the_request(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RefundPaymentRequest('pi_1', 0);
    }

    public function test_checkout_session_requests_are_recorded_with_their_custom_text(): void
    {
        $this->gateway->willReturnResponse('checkout_session', new Session('https://checkout.test/s'));
        $lineItems = new LineItemCollection();
        $lineItems->add(LineItem::fromPriceId('price_1', 1));

        $this->gateway->createCheckoutSession(new CreateCheckoutSessionRequest(
            new Customer('cus_1', []),
            $lineItems,
            'https://app.test/ok',
            'https://app.test/cancel',
            [],
            new CustomText(submit: 'You can cancel within 14 days.'),
        ));

        self::assertSame('You can cancel within 14 days.', $this->gateway->checkoutSessionRequests[0]->customText?->submit);
    }

    private function payment(string $paymentId, string $paidOn, int $amount = 1500): SubscriptionPayment
    {
        $paidAt = new \DateTimeImmutable($paidOn);

        return new SubscriptionPayment('sub_1', 'in_'.$paymentId, $paymentId, $amount, 'gbp', $paidAt, $paidAt, $paidAt->modify('+1 month'));
    }
}
