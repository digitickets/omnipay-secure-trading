<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * CompletePurchase Request
 *
 * @method Response send()
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'AUTH';
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMd($value)
    {
        return $this->setParameter('md', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaRes($value)
    {
        return $this->setParameter('paRes', $value);
    }

    /**
     * @return DOMDocument
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('md', 'paRes');

        $data = $this->getBaseData();

        /** @var DOMDocument $request */
        $request = $data->getElementsByTagName('request')->item(0);

        /** @var DOMDocument $operation */
        $operation = $request->getElementsByTagName('operation')->item(0);
        $operation->appendChild($data->createElement('accounttypedescription', 'ECOM'));
        $operation->appendChild($data->createElement('authmethod', 'FINAL'));

        $operation->appendChild($data->createElement('md', $this->getMd()));
        $operation->appendChild($data->createElement('pares', $this->getPaRes()));

        return $data;
    }

    /**
     * return string
     */
    public function getMd()
    {
        return $this->getParameter('md');
    }

    /**
     * return string
     */
    public function getPaRes()
    {
        return $this->getParameter('paRes');
    }
}
