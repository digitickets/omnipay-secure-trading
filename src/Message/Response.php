<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Response
 */
class Response extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data->response->error->code) && ((string)$this->data->response->error->code) === '0';
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * @return null|string
     */
    public function getTransactionReference()
    {
        return isset($this->data->response->transactionreference) ?
            (string)$this->data->response->transactionreference : null;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return isset($this->data->response->error->message) ? (string)$this->data->response->error->message : null;
    }

    /**
     * @return null|int
     */
    public function getCode()
    {
        return isset($this->data->response->error->code) ? (int)$this->data->response->error->code : null;
    }

    /**
     * @return null|string
     */
    public function getCardReference()
    {
        return $this->getCardReference();
    }
}
