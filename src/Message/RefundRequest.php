<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

/**
 * Refund Request
 *
 * @method Response send()
 */
class RefundRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'REFUND';
    }

    /**
     * @return SimpleXMLElement
     */
    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;

        /** @var SimpleXmlElement $operation */
        $operation = $request->operation;
        $operation->addChild('parenttransactionreference', $this->getTransactionReference());

        if (!is_null($this->getAmount())) {
            $billing = $request->addChild('billing');
            $billing->addChild('amount', $this->getAmountInteger());
        }

        $card = $this->getCard();
        if ($card && $card->getEmail()) {
            /** @var SimpleXmlElement $merchant */
            $merchant = $request->merchant;
            $merchant->addChild('email', $card->getEmail());
        }

        return $data;
    }
}
