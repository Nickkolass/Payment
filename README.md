## Установка проекта

Требуется наличие Ubuntu с установленными на него composer, git, docker.

В терминале Ubuntu перейти в нужную директорию и запустить команды:

-     git clone https://github.com/Nickkolass/payment.git
-     composer install
-     cp .env.example .env
-     docker compose up -d
- В проекте подключен сервис YOO_KASSA, для работы которого используется https прокси NGROK. В целях предоставления
  возможности просмотра функционала без соответствующих токенов, по умолчанию приложение настроено на использование
  заглушки. Для активации сервиса YOO_KASSA необходимо:
    - в файле .env в поле PAYMENT_CONNECTION установить значение 'yookassa', в поле NGROK_TOKEN установить значение
      токена для сервиса ngrok
    - в терминале выполнить команды
    -     docker exec -it payment_app bash
    -     ./ngrok customers add-authtoken $token // вставить токен ngrok
    -     ./ngrok http host.docker.internal:8877
    - скопировать значение, выведенное в результате выполнения команд в терминале в поле "Forwarding" с добавлением
      "/api/payment/callback" например https://d93f-217-65-215-218.ngrok-free.app/api/payment/callback
    - в личном кабинете yookassa https://yookassa.ru/my/merchant/integration/http-notifications, а также
      https://yookassa.ru/my/gate/integration/webhooks в полях "URL для уведомлений" установить скопированное значение.
      В графах "О каких событиях уведомлять" выбрать все.
-     docker exec -it payment_app bash
    -     php artisan init

## Начало работы

Для смены способа уведомления базового проекта (shop) о проведенных операциях в графе 'reply_to_driver'
файла app/Components/Transport/Consumer/customers/65a4da542d4844.php установить значение 'amqp' либо 'http'.

При выборе в базовом проекте (shop) способа доставки сообщений до платежной системы посредством amqp протокола выполнить
следующие команды. При этом требующие немедленного ответа запросы буду направляться по http вне зависимости от настроек.

-     docker exec -it payment_app bash
    -     php artisan amqp:consume payment

