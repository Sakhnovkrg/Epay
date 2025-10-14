<?php

namespace Sakhnovkrg\Epay\Helper;

class PaymentHelper
{
    /**
     * Форматирует номер заказа, дополняя нулями слева до 6 символов.
     *
     * @param string|int $invoiceId Номер заказа
     * @return string Отформатированный номер заказа
     */
    public static function formatInvoiceId(string|int $invoiceId): string
    {
        return str_pad((string) $invoiceId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Нормализует номер телефона, удаляя все нецифровые символы.
     *
     * @param string $phone Номер телефона в любом формате
     * @return string Нормализованный номер (только цифры)
     */
    public static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }
}
