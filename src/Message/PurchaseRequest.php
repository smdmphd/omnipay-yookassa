<?php
/**
 * Yoo.Kassa driver for Omnipay payment processing library
 *
 * @link      https://github.com/hiqdev/omnipay-yoo-kassa
 * @package   omnipay-yoo-kassa
 * @license   MIT
 * @copyright Copyright (c) 2019, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Throwable;

/**
 * Class PurchaseRequest.
 *
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency', 'returnUrl', 'transactionId', 'description', 'capture', 'receipt');

        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'return_url' => $this->getReturnUrl(),
            'transactionId' => $this->getTransactionId(),
            'capture' => $this->getCapture(),
            'receipt' => $this->getReceipt(),
        ];
    }

    public function sendData($data)
    {
        try {
            $paymentResponse = $this->client->createPayment([
                'amount' => [
                    'value' => $data['amount'],
                    'currency' => $data['currency'],
                ],
                'description' => $data['description'],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $data['return_url'],
                ],
                'capture' => $data['capture'],
                'metadata' => [
                    'transactionId' => $data['transactionId'],
                ],
                'receipt' => $data['receipt'],
            ], $this->makeIdempotencyKey());

            return $this->response = new PurchaseResponse($this, $paymentResponse);
        } catch (\Throwable $e) {
            throw new InvalidRequestException('Failed to request purchase: ' . $e->getMessage(), 0, $e);
        }
    }

    private function makeIdempotencyKey(): string
    {
        $data = $this->getData();
        if (isset($data['receipt'])) {
            $data['receipt'] = json_encode($data['receipt']);
        }

        return md5(implode(',', array_merge(['create'], $data)));
    }
}
