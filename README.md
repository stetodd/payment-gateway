# stetodd/payment-gateway

Provider-neutral payment gateway contracts: `PaymentGatewayInterface`, request/response models, and a `SimulatorPaymentGateway` test double.

Implementations live in separate packages (e.g. `stetodd/stripe-gateway-bundle`).

## Install

```bash
composer require stetodd/payment-gateway
```

## Usage

Type-hint `Stetodd\PaymentGateway\PaymentGatewayInterface` in your application code. Bind it to a concrete gateway implementation in your container.

In tests, use `Stetodd\PaymentGateway\Testing\SimulatorPaymentGateway` and queue responses with `willReturnResponse(string $key, object $response)`.
