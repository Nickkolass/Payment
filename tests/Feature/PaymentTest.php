<?php


use App\Components\Payment\AbstractPaymentClient;
use App\Http\Services\PaymentCallbackService;
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

        $this->postJson($route, $data)->assertUnprocessable();

        $this->withoutExceptionHandling();
        $data = ['data' => json_encode($data)];
        $this->postJson($route, $data)->assertOk();
    }

    /**@test */
    public function test_a_pay_can_be_done(): void
    {
        $data = [
            'order_id' => rand(),
            'price' => rand(),
            'return_url' => route('payment.card.validate'),
        ];
        $route = route('payment.pay');
        $this->partialMock(PaymentCallbackService::class, function (MockInterface $mock) {
            $mock->shouldReceive('callback')->between(0,1);
        });

        $this->withoutExceptionHandling();

        $this->post($route, $data)
            ->assertOk()
            ->assertSeeText('http:');
    }

    /**@test */
    public function test_a_payout_can_be_done(): void
    {
        $data = [
            'order_id' => rand(),
            'price' => rand(),
            'payout_token' => Str::random(20),
        ];
        $route = route('payment.payout');
        $this->partialMock(PaymentCallbackService::class, function (MockInterface $mock) {
            $mock->shouldReceive('callback')->between(0,1);
        });

        $this->withoutExceptionHandling();

        $this->post($route, $data)->assertOk();
    }

    /**@test */
    public function test_a_refund_can_be_done(): void
    {
        $data = [
            'order_id' => rand(),
            'price' => rand(),
            'pay_id' => Str::random(20),
        ];
        $route = route('payment.refund');
        $this->partialMock(PaymentCallbackService::class, function (MockInterface $mock) {
            $mock->shouldReceive('callback')->between(0,1);
        });

        $this->withoutExceptionHandling();

        $this->post($route, $data)->assertOk();
    }
}
