<?php

namespace Sakhnovkrg\Epay\Validator;

use Sakhnovkrg\Epay\Exceptions\ValidationException;

class PaymentValidator
{
    public function validate(array $data): void
    {
        $errors = [];

        if (empty($data['invoiceId'])) {
            $errors['invoiceId'] = 'Номер заказа обязателен';
        } elseif (!ctype_digit((string) $data['invoiceId'])) {
            $errors['invoiceId'] = 'Номер заказа должен содержать только цифры';
        } elseif (strlen((string) $data['invoiceId']) < 6) {
            $errors['invoiceId'] = 'Номер заказа должен содержать минимум 6 цифр';
        } elseif (strlen((string) $data['invoiceId']) > 15) {
            $errors['invoiceId'] = 'Номер заказа не должен превышать 15 символов';
        }

        if (empty($data['amount'])) {
            $errors['amount'] = 'Сумма обязательна';
        } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'Сумма должна быть положительным числом';
        }

        if (empty($data['currency'])) {
            $errors['currency'] = 'Валюта обязательна';
        }

        if (empty($data['terminal'])) {
            $errors['terminal'] = 'Терминал обязателен';
        }

        if (empty($data['backLink'])) {
            $errors['backLink'] = 'Ссылка возврата обязательна';
        } elseif (!filter_var($data['backLink'], FILTER_VALIDATE_URL)) {
            $errors['backLink'] = 'Ссылка возврата должна быть валидным URL';
        }

        if (empty($data['postLink'])) {
            $errors['postLink'] = 'Ссылка для уведомлений обязательна';
        } elseif (!filter_var($data['postLink'], FILTER_VALIDATE_URL)) {
            $errors['postLink'] = 'Ссылка для уведомлений должна быть валидным URL';
        }

        if (!empty($data['failureBackLink']) && !filter_var($data['failureBackLink'], FILTER_VALIDATE_URL)) {
            $errors['failureBackLink'] = 'Ссылка возврата при ошибке должна быть валидным URL';
        }

        if (!empty($data['failurePostLink']) && !filter_var($data['failurePostLink'], FILTER_VALIDATE_URL)) {
            $errors['failurePostLink'] = 'Ссылка для уведомлений об ошибке должна быть валидным URL';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email должен быть валидным';
        }

        if (!empty($data['phone'])) {
            if (!ctype_digit((string) $data['phone'])) {
                $errors['phone'] = 'Телефон должен содержать только цифры';
            } elseif (!preg_match('/^7\d{10}$/', (string) $data['phone'])) {
                $errors['phone'] = 'Телефон должен быть в формате 7XXXXXXXXXX (11 цифр, начинается с 7)';
            }
        }

        if (!empty($data['language']) && !in_array($data['language'], ['RU', 'KZ', 'EN'])) {
            $errors['language'] = 'Язык должен быть RU, KZ или EN';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Описание обязательно';
        } elseif (strlen((string) $data['description']) > 125) {
            $errors['description'] = 'Описание не должно превышать 125 байт';
        }

        if (!empty($data['invoiceIdAlt'])) {
            if (!ctype_digit((string) $data['invoiceIdAlt'])) {
                $errors['invoiceIdAlt'] = 'Альтернативный номер заказа должен содержать только цифры';
            } elseif (strlen((string) $data['invoiceIdAlt']) < 6) {
                $errors['invoiceIdAlt'] = 'Альтернативный номер заказа должен содержать минимум 6 цифр';
            } elseif (strlen((string) $data['invoiceIdAlt']) > 15) {
                $errors['invoiceIdAlt'] = 'Альтернативный номер заказа не должен превышать 15 символов';
            }
        }

        if (!empty($data['name']) && !preg_match('/^[a-zA-Z\s]+$/', (string) $data['name'])) {
            $errors['name'] = 'Имя плательщика должно содержать только латинские буквы';
        }

        if ($errors !== []) {
            throw new ValidationException($errors, 'Ошибка валидации данных');
        }
    }
}
