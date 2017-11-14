<?php

namespace Omnipay\SecureTrading\Message;

use DOMDocument;

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
     * @return DOMDocument
     */
    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();

        /** @var DOMDocument $request */
        $request = $data->getElementsByTagName('request')->item(0);

        /** @var DOMDocument $operation */
        $operation = $request->getElementsByTagName('operation')->item(0);
        $operation->appendChild($data->createElement('parenttransactionreference', $this->getTransactionReference()));

        if (!is_null($this->getAmount())) {
            $billing = $request->appendChild($data->createElement('billing'));
            $billing->appendChild($data->createElement('amount', $this->getAmountInteger()));
        }

        $card = $this->getCard();
        if ($card && $card->getEmail()) {
            /** @var DOMDocument $merchant */
            $merchant = $request->getElementsByTagName('merchant')->item(0);
            $merchant->appendChild($data->createElement('email', $card->getEmail()));
        }

        return $data;
    }
}
