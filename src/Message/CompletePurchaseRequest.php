<?php

namespace Omnipay\SecureTrading\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use SimpleXMLElement;

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
     * @return SimpleXMLElement
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('md', 'paRes');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;

        /** @var SimpleXmlElement $operation */
        $operation = $request->operation;
        $operation->addChild('accounttypedescription', 'ECOM');
        $operation->addChild('authmethod', 'FINAL');

        $operation->addChild('md', $this->getMd());
        $operation->addChild('pares', $this->getPaRes());

        return $data;
    }
}
