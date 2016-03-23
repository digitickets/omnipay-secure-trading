<?php

namespace Omnipay\SecureTrading\Message;

use SimpleXMLElement;

class TransactionUpdateRequest extends AbstractRequest
{
    const ACTION = 'TRANSACTIONUPDATE';

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
    public function setUpdates(array $value)
    {
        return $this->setParameter('updates', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * Possible values for updates:
     * [
     *     'merchant' => [
     *         'orderreference' => 255
     *     ],
     *     'settlement' => [
     *         'settlebaseamount' => 0,
     *         'settleduedate' => '01-01-2015',
     *         'settlemainamount' => 200,
     *         'settlemainamountcurrencycode' => 'EUR',
     *         'settlestatus' => 1
     *      ]
     * ]
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('updates');

        $data = $this->getBaseData();

        /** @var SimpleXmlElement $request */
        $request = $data->request;

        $filter = $request->addChild('filter');
        $siteReference = $filter->addChild('sitereference', $this->getSiteReference());
        $transactionReference = $filter->addChild('transactionreference', $this->getTransactionReference());

        $updates = $request->addChild('updates');

        $updateChilds = [
            'merchant' => [
                'orderreference' => 255
            ],
            'settlement' => [
                'settlebaseamount' => 0,
                'settleduedate' => '01-01-2015',
                'settlemainamount' => 200,
                'settlemainamountcurrencycode' => 'EUR',
                'settlestatus' => 1
            ]
        ];

        $this->appendUpdates($updates, $this->getParameter('updates'));

        return $data;
    }

    private function appendUpdates(SimpleXmlElement &$parent, array $updates)
    {
        foreach ($updates as $node => $value) {
            if (is_array($value)) {
                $child = $parent->addChild($node);
                $this->appendUpdates($child, $value);
            } else {
                $parent->addChild($node, $value);
            }
        }
    }
}
