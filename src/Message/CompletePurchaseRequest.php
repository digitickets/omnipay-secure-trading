<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use DOMDocument;

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
     * return string
     */
    public function getMd()
    {
        return $this->getParameter('md');
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
     * return string
     */
    public function getPaRes()
    {
        return $this->getParameter('paRes');
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
}
