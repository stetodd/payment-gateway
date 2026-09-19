<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway;

use Stetodd\PaymentGateway\Model\Checkout\CheckoutSession;
use Stetodd\PaymentGateway\Model\Checkout\Session;
use Stetodd\PaymentGateway\Model\Customer;
use Stetodd\PaymentGateway\Model\Payment\Payment;
use Stetodd\PaymentGateway\Model\Payment\Refund;
use Stetodd\PaymentGateway\Model\Request\Checkout\CreateCheckoutSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Checkout\GetCheckoutSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Customer\CreateCustomerRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CancelPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CapturePaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CreatePaymentHoldRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\GetPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\RefundPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Portal\CreatePortalSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\CancelSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\GetSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\ReactivateSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\UpdateSubscriptionPlanRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\UpdateSubscriptionQuantityRequest;
use Stetodd\PaymentGateway\Model\Subscription;
use Stetodd\PaymentGateway\Model\Subscription\SubscriptionPayment;

interface PaymentGatewayInterface
{
    public function createCheckoutSession(CreateCheckoutSessionRequest $request): Session;

    /**
     * How a checkout ended, read straight from the vendor, for a caller that
     * cannot wait for — or never received — the webhook. Null when the vendor
     * has no such session.
     */
    public function findCheckoutSession(GetCheckoutSessionRequest $request): ?CheckoutSession;

    /**
     * Closes an open checkout so it can never be paid. Doing this to a session
     * that is already settled changes nothing.
     */
    public function expireCheckoutSession(GetCheckoutSessionRequest $request): void;

    public function createCustomer(CreateCustomerRequest $request): Customer;

    public function cancelSubscription(CancelSubscriptionRequest $request): Subscription;

    public function reactivateSubscription(ReactivateSubscriptionRequest $request): void;

    public function updateSubscriptionPlan(UpdateSubscriptionPlanRequest $request): void;

    public function updateSubscriptionQuantity(UpdateSubscriptionQuantityRequest $request): void;

    public function createPortalSession(CreatePortalSessionRequest $request): string;

    public function getSubscription(GetSubscriptionRequest $request): Subscription;

    public function findSubscription(GetSubscriptionRequest $request): ?Subscription;

    /**
     * The subscription's most recent paid invoice, or null when nothing has
     * been paid on it yet.
     *
     * @throws \Stetodd\PaymentGateway\Exception\Subscription\SubscriptionNotFoundException
     */
    public function findLatestSubscriptionPayment(GetSubscriptionRequest $request): ?SubscriptionPayment;

    /**
     * A hosted checkout that authorises a one-off amount without capturing it.
     * The completed-checkout webhook carries the payment id to capture or
     * cancel with.
     */
    public function createPaymentHoldSession(CreatePaymentHoldRequest $request): Session;

    public function capturePayment(CapturePaymentRequest $request): Payment;

    /** Releases an authorised hold (or cancels an unpaid payment). */
    public function cancelPayment(CancelPaymentRequest $request): Payment;

    /**
     * @throws \Stetodd\PaymentGateway\Exception\Payment\PaymentNotFoundException
     */
    public function getPayment(GetPaymentRequest $request): Payment;

    /**
     * Returns money from a captured payment to the customer's payment method.
     * Partial refunds may follow one another up to the amount captured. A
     * request carrying an idempotency key pays at most once: a repeat returns
     * the refund the first one made.
     *
     * @throws \Stetodd\PaymentGateway\Exception\Payment\PaymentNotFoundException
     * @throws \Stetodd\PaymentGateway\Exception\Payment\RefundFailedException
     */
    public function refundPayment(RefundPaymentRequest $request): Refund;
}
