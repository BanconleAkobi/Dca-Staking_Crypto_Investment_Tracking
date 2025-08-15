<?php

namespace App\DataFixtures;

use App\Entity\Crypto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CryptoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {
        foreach ([['BTC','Bitcoin'],['SOL','Solana'],['ETH','Ethereum']] as [$sym,$name]) {
            $crypto = (new Crypto())->setSymbol($sym)->setName($name);
            $manager->persist($crypto);
        }
        $manager->flush();
    }

}
