<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

class TransactionQueryRequest extends AbstractRequest
{
    const ACTION = 'TRANSACTIONQUERY';

    /**
     * @return string
     */
    public function getAction()
    {
        return self::ACTION;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setTransactionReferences(array $value)
    {
        return $this->setParameter('transactionReferences', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('transactionReferences');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;

        $filter = $request->addChild('filter');
        $siteReference = $filter->addChild('sitereference', $this->getSiteReference());

        foreach ($this->getParameter('transactionReferences') as $transactionReference) {
            $filter->addChild('transactionreference', $transactionReference);
        }

        return $data;
    }

    /**
     * @param string $data
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new TransactionQueryResponse($this, $data);
    }
}
