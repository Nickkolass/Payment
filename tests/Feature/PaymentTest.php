<?php


namespace Tests\Feature;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\PaymentClientInterface;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentTest extends TestCase
{

    /**@test */
    public function test_a_card_widget_view_can_be_getted(): void
    {
        $data = [
            '_token' => Str::random(20),
            'return_url' => route('payment.card.validate'),
        ];

        $route = route('payment.card.widget');

        $this->withoutExceptionHandling();

        $res = $this->post($route, $data)
            ->assertOk()
            ->getContent();
        $this->assertIsString($res);
    }

    /**@test */
    public function test_a_card_widget_view_can_be_validate(): void
    {
        $data = [
            'payout_token' => Str::random(20),
            'first6' => fake()->numerify('######'),
            'last4' => fake()->numerify('####'),
            'card_type' => fake()->creditCardType(),
            'issuer_country' => fake()->country(),
        ];
        $route = route('payment.card.validate');

        $this->withoutExceptionHandling();
        $this->postJson($route, $data)->assertOk();
    }

    /**@test */
    public function test_a_payment_can_be_done(): void
    {
        $data = [
            'order_id' => rand(),
            'price' => rand(),
        ];
        $headers = ['requester-id' => array_key_first(config('consumer.customers'))];

        $route = route('payment.payment');

        $this->withoutExceptionHandling();

        foreach (AbstractPaymentClient::getPaymentTypes() as $paymentType) {

            $data['payment_type'] = $paymentType;
            $payment_type = match ($paymentType) {
                PaymentClientInterface::CALLBACK_PAYMENT_TYPE_PAY => ['return_url' => fake()->url()],
                PaymentClientInterface::CALLBACK_PAYMENT_TYPE_PAYOUT => ['payout_token' => uniqid()],
                PaymentClientInterface::CALLBACK_PAYMENT_TYPE_REFUND => ['pay_id' => uniqid()],
            };
            $data += $payment_type;

            foreach (config('consumer.drivers') as $driver) {
                $this->partialMock($driver, function (MockInterface $mock) {
                    $mock->shouldReceive('notify')->between(0, 1);
                });
            }

            $this->withHeaders($headers)
                ->post($route, $data)
                ->assertOk();

            array_pop($data);
        }
    }
}
