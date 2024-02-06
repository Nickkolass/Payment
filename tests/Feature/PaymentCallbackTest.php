<?php


use App\Components\Payment\AbstractPaymentClient;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentCallbackTest extends TestCase
{

    /**@test */
    public function test_a_callback_can_be_accepted(): void
    {
        $data = [
            'event' => AbstractPaymentClient::CALLBACK_EVENT_PAY,
            'order_id' => rand(),
        ];
        $route = route('payment.callback');

        $this->withoutExceptionHandling();

        $this->partialMock(AbstractPaymentClient::getClientName(), function (MockInterface $mock) {
            $mock->shouldReceive('sendCallbackNotification')->once();
        });
        $this->post($route, $data)->assertOk();
    }
}
