<?php

namespace App\Service;

use App\Entity\Crypto;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;

class TimeSeriesBuilder
{

    public function __construct(private TransactionRepository $repo, private TransactionCalculator $calc) {

    }

    /** @return array{labels: string[], quantityCumulative: float[], investedCumulative: float[]} */
    public function build(User $u, Crypto $a): array
    {
        $txs = $this->repo->findForUserAndCryptoChrono($u, $a);
        $labels = []; $qCum = []; $invCum = [];
        $q = 0.0; $inv = 0.0;
        foreach ($txs as $t) {
            $labels[] = $t->getDate()->format('Y-m-d');
            $q += (float)$t->getQuantity();
            if ($t->getType() === Transaction::TYPE_BUY) {
                $inv += $this->calc->investedUsd($t);
            }
            $qCum[] = round($q, 10);
            $invCum[] = round($inv, 2);
        }
        return ['labels'=>$labels, 'quantityCumulative'=>$qCum, 'investedCumulative'=>$invCum];
    }
}
