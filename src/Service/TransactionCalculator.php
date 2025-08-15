<?php

namespace App\Service;

use App\Entity\Transaction;

class TransactionCalculator
{
    public function investedUsd(Transaction $transaction){
        if($transaction->getType() !== Transaction::TYPE_BUY){
            return 0.0;
        }
        $price = (float)($transaction->getUnitPriceUsd() ?? 0);
        $qty = (float)($transaction->getQuantity() ?? 0);
        $fee = (float)($transaction->getFeeUsd() ?? 0);
        return $price * $qty + $fee;
    }

}
