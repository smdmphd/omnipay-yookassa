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
 * Class CompletePurchaseRequest.
 */
class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('shopId', 'secret', 'transactionId', 'transactionReference', 'amount', 'currency');

        return $this->httpRequest->request->all();
    }

    /**
     * @param mixed $data
     * @throws InvalidRequestException
     * @return \Omnipay\Common\Message\ResponseInterface|CaptureResponse
     */
    public function sendData($data)
    {
        try {
            $result = $this->client->capturePayment([
                'amount' => [
                    'value' => $this->getAmount(),
                    'currency' => $this->getCurrency(),
                ],
            ], $this->getTransactionReference(), 'capture-' . $this->getTransactionId());

            return $this->response = new CaptureResponse($this, $result);
        } catch (Throwable $e) {
            throw new InvalidRequestException('Failed to capture payment: ' . $e->getMessage(), 0, $e);
        }
    }
}
