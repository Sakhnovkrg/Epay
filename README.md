# Halyk Bank Epay

[![Tests](https://github.com/sakhnovkrg/epay/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/sakhnovkrg/epay/actions/workflows/tests.yml)
[![Coverage](https://img.shields.io/badge/coverage-83.85%25-brightgreen)](https://github.com/sakhnovkrg/epay)
[![Latest Version](https://img.shields.io/packagist/v/sakhnovkrg/epay?label=version&color=blue)](https://packagist.org/packages/sakhnovkrg/epay)
[![PHP Version](https://img.shields.io/badge/php-8.1%20%7C%208.2%20%7C%208.3%20%7C%208.4-777BB4?logo=php&logoColor=white)](https://packagist.org/packages/sakhnovkrg/epay)
[![License](https://img.shields.io/packagist/l/sakhnovkrg/epay?color=blue)](https://github.com/sakhnovkrg/epay/blob/main/LICENSE)

Интеграция с платёжным шлюзом Epay (В разработке)

- 💳 оплата через платежную страницу

- 🔄 проверка статусов платежей

- 🧩 удобное и типизированное API

## Требования

- PHP 8.1+
- ext-curl
- ext-json

## Установка

```bash
composer require sakhnovkrg/epay
```

## Быстрый старт

### Создание платежа

```php
<?php

use Sakhnovkrg\Epay\Epay;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\Helper\PaymentHelper;

$config = new EpayConfig(
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    terminal: 'your-terminal-id',
    isTest: true
);

$epay = new Epay($config);

// secret_hash - обязательный параметр, генерируется для каждого платежа
// Используется для верификации webhook
$payment = $epay->payment('unique-secret-hash-123')
    ->invoiceId(PaymentHelper::formatInvoiceId(1)) // Вернет '000001'
    ->amount(1000)
    ->description('Оплата заказа #1')
    ->backLink('https://example.com/success')
    ->failureBackLink('https://example.com/failure') // Опционально: куда вернуть при ошибке
    ->postLink('https://example.com/webhook')
    ->failurePostLink('https://example.com/webhook/failure') // Опционально: webhook при ошибке
    ->build();

echo $epay->render($payment);
```

### Использование в SPA (React, Vue, Angular)

Для SPA-приложений можно получить данные платежа в виде массива и самостоятельно вызвать виджет:

```php
<?php

// API endpoint для создания платежа
$payment = $epay->payment('unique-secret-hash-123')
    ->invoiceId(PaymentHelper::formatInvoiceId(1))
    ->amount(1000)
    ->description('Оплата заказа #1')
    ->backLink('https://example.com/success')
    ->failureBackLink('https://example.com/failure')
    ->postLink('https://example.com/webhook')
    ->failurePostLink('https://example.com/webhook/failure')
    ->build();

// Получить URL виджета и данные платежа
$response = [
    'widgetUrl' => $epay->getWidgetUrl(),
    'paymentData' => $epay->getPaymentData($payment)
];

header('Content-Type: application/json');
echo json_encode($response);
```

Затем в вашем SPA:

```javascript
// Загрузить скрипт виджета один раз
const script = document.createElement('script');
script.src = response.widgetUrl;
document.body.appendChild(script);

// Когда скрипт загружен, вызвать виджет с данными
script.onload = () => {
    window.halyk.pay(response.paymentData);
};
```

### Обработка webhook

```php
<?php

use Sakhnovkrg\Epay\DTO\WebhookPayload;
use Sakhnovkrg\Epay\DTO\WebhookFailure;

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['code']) && $data['code'] === 'ok') {
    $webhook = WebhookPayload::fromArray($data);

    // ВАЖНО: Проверьте secret_hash для защиты от подделки webhook
    // Достаньте secret_hash, который вы сохранили при создании платежа
    $expectedSecretHash = getStoredSecretHash($webhook->invoiceId); // из БД

    if ($webhook->secretHash !== $expectedSecretHash) {
        http_response_code(400);
        exit('Invalid secret_hash');
    }

    // Обработка успешного платежа
    $invoiceId = $webhook->invoiceId;
    $amount = $webhook->amount;

    // Обновите статус заказа в вашей базе данных
    // updateOrderStatus($invoiceId, 'paid');

} else {
    $webhook = WebhookFailure::fromArray($data);

    // Проверка secret_hash и при ошибке
    $expectedSecretHash = getStoredSecretHash($webhook->invoiceId); // из БД

    if ($webhook->secretHash !== $expectedSecretHash) {
        http_response_code(400);
        exit('Invalid secret_hash');
    }

    // Обработка ошибки платежа
    $reason = $webhook->reason;
    $reasonCode = $webhook->reasonCode;

    // Обновите статус заказа
    // updateOrderStatus($webhook->invoiceId, 'failed', $reason);
}

http_response_code(200);
echo "OK";
```

### Проверка статуса

```php
<?php

$status = $epay->checkStatus('123456');

if ($status->isSuccess() && $status->hasTransaction()) {
    $transaction = $status->transaction;

    if ($transaction->isCharged()) {
        // Оплачено
    } elseif ($transaction->isFailed()) {
        // Ошибка
    }
}
```

## Хелперы для форматирования

Пакет предоставляет хелперы для форматирования данных:

```php
use Sakhnovkrg\Epay\Helper\PaymentHelper;

// Форматирование номера заказа (дополнение нулями до 6 символов)
PaymentHelper::formatInvoiceId('123');      // '000123'
PaymentHelper::formatInvoiceId(1);          // '000001'
PaymentHelper::formatInvoiceId('123456');   // '123456'
PaymentHelper::formatInvoiceId(1234567);    // '1234567' (не изменяется)

// Нормализация телефона (удаление всех нецифровых символов)
PaymentHelper::normalizePhone('+7 (700) 123-45-67');  // '77001234567'
PaymentHelper::normalizePhone('7-700-123-45-67');     // '77001234567'
PaymentHelper::normalizePhone('+7(700)1234567');      // '77001234567'
```

**Важно:** PaymentBuilder не нормализует данные автоматически. Используйте хелперы явно:

```php
$payment = $epay->payment('my-secret-hash')
    ->invoiceId(PaymentHelper::formatInvoiceId(123))  // Принимает string или int
    ->amount(1000)
    ->description('Оплата')
    ->backLink('https://example.com/success')
    ->failureBackLink('https://example.com/failure')
    ->postLink('https://example.com/webhook')
    ->failurePostLink('https://example.com/webhook/failure')
    ->phone(PaymentHelper::normalizePhone('+7 (700) 123-45-67'))
    ->build();
```

## Обработка ошибок

```php
use Sakhnovkrg\Epay\Exceptions\ValidationException;
use Sakhnovkrg\Epay\Exceptions\AuthenticationException;
use Sakhnovkrg\Epay\Exceptions\EpayException;

try {
    $payment = $epay->payment('my-secret-hash')
        ->invoiceId(PaymentHelper::formatInvoiceId('123'))
        ->amount(1000)
        ->description('Оплата заказа')
        ->backLink('https://example.com/success')
        ->postLink('https://example.com/webhook')
        ->build();

} catch (ValidationException $e) {
    // Ошибка валидации
    $errors = $e->getErrors();
    // ['invoiceId' => 'Номер заказа обязателен', ...]

} catch (AuthenticationException $e) {
    // Ошибка аутентификации OAuth
    echo $e->getMessage();

} catch (EpayException $e) {
    // Другие ошибки
    echo $e->getMessage();
}
```