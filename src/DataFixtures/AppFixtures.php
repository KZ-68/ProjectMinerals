<?php

namespace App\DataFixtures;

use App\Entity\Mineral;
use App\Service\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 50; $i++) { 
            $mineral = new Mineral();
            $mineral->setName('Mineral #1')
                ->setFormula('Formula #1')
                ->setCrystalSystem('Crystal System #1')
                ->setDensity(2.50)
                ->setHardness(4)
                ->setFracture('Fracture #1')
                ->setStreak('Streak #1');
                
            $manager->persist($mineral);
        }

        $manager->flush();
    }
}
