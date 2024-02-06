@php use App\Components\Payment\AbstractPaymentClient; @endphp
<div id="payout-form"></div>

<script src="https://yookassa.ru/payouts-data/3.0.0/widget.js"></script>
<script type="text/javascript">
    //Инициализация виджета. Все параметры обязательные.
    const payoutsData = new window.PayoutsData({
        type: 'payout',
        account_id: {!! json_encode(AbstractPaymentClient::getAgentLogin()) !!}, //Идентификатор шлюза (agentId в личном кабинете)
        success_callback(data) {
            //Обработка ответа с токеном карты
            var form = document.createElement('form');
            form.method = 'post';
            form.action = {!! json_encode($return_url) !!};
            document.body.appendChild(form);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = {!! json_encode($_token) !!};
            form.appendChild(input);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_method';
            input.value = 'patch';
            form.appendChild(input);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'data';
            input.value = JSON.stringify(data);
            form.appendChild(input);
            form.submit();
        },
        error_callback(error) {
            //Обработка ошибок инициализации
        }
    });

    //Отображение формы в контейнере
    payoutsData.render('payout-form')
        //Метод возвращает Promise, исполнение которого говорит о полной загрузке формы сбора данных (можно не использовать).
        .then(() => {
            //Код, который нужно выполнить после отображения формы.
        });

</script>
