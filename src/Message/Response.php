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
        return $this->getCode() === 0 && $this->getSettleStatus() !== 3;
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
     * @return null|int
     */
    public function getErrorData()
    {
        return isset($this->data->response->error->data) ? (string)$this->data->response->error->data : null;
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
        return isset($this->data->response->transactionreference)
            ? (string)$this->data->response->transactionreference : null;
    }

    /**
     * @return null|string
     */
    public function getCardReference()
    {
        return $this->getTransactionReference();
    }

    /**
     * @return null|int
     */
    public function getSettleStatus()
    {
        return isset($this->data->response->settlement->settlestatus)
            ? (int)$this->data->response->settlement->settlestatus : null;
    }

    /**
     * @return null|string date format: "Y-m-d"
     */
    public function getSettleDueDate()
    {
        return isset($this->data->response->settlement->settleduedate)
            ? (string)$this->data->response->settlement->settleduedate : null;
    }
}
