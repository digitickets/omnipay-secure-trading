<?php

namespace Omnipay\SecureTrading\Message;

class TransactionQueryResponse extends Response
{
    const SETTLE_PENDING = 0;
    const SETTLE_MANUAL = 1;
    const SETTLE_SETTLING = 10;
    const SETTLE_SETTLED = 100;
    const SETTLE_SUSPENDED = 2;
    const SETTLE_CANCELED = 3;

    public function getRecords()
    {
        return $this->data->xpath('response/record');
    }

    public function getOrderStatusForRecord(\SimpleXMLElement $record)
    {
        return (int) ((string) $record->xpath('settlement/settlestatus')[0]);
    }

    public function getOrderReferenceForRecord(\SimpleXMLElement $record)
    {
        return (string) $record->xpath('transactionreference')[0];
    }

    public function getTransactionReferenceForRecord(\SimpleXMLElement $record)
    {
        return (string) $record->xpath('merchant/orderreference')[0];
    }
}
