<?php

namespace Sakhnovkrg\Epay\Widget;

use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\PaymentRequest;

class WidgetRenderer
{
    public function __construct(
        private readonly EpayConfig $config
    ) {
    }

    public function render(PaymentRequest $payment): string
    {
        $widgetUrl = $this->config->getWidgetUrl();
        $paymentData = json_encode($payment->toArray());

        return <<<HTML
<script src="{$widgetUrl}"></script>
<script>
    halyk.pay({$paymentData});
</script>
HTML;
    }

    public function renderInline(PaymentRequest $payment): string
    {
        return $this->render($payment);
    }

    public function getPaymentData(PaymentRequest $payment): array
    {
        return $payment->toArray();
    }

    public function getWidgetUrl(): string
    {
        return $this->config->getWidgetUrl();
    }
}
