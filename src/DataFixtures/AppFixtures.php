<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Mineral;
use App\Entity\Variety;
use App\Entity\Category;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    private $minerals;

    private $varieties;

    protected SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->faker = Factory::create('fr_FR');
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->addVarieties($manager);
        $this->addMinerals($manager);
        $this->addCategories($manager);

        $manager->flush();
    }

    private function addVarieties(EntityManager $em)
    {
        for ($i=0; $i < 100; $i++) { 
            $variety = new Variety();
            $variety->setName($this->faker->word())
            ->setSlug($this->slugger->slug($variety->getName()));;
            
            $em->persist($variety);
            $this->varieties[] = $variety;
        }
        
    }

    public function addMinerals(EntityManager $em) {
        for ($i=0; $i < 100; $i++) { 
            $mineral = new Mineral();
            $mineral->setName($this->faker->word())
                ->setFormula($this->faker->word())
                ->setCrystalSystem($this->faker->word())
                ->setDensity($this->faker->randomFloat())
                ->setHardness($this->faker->randomDigitNotNull())
                ->setFracture($this->faker->word())
                ->setStreak($this->faker->safeColorName())
                ->setSlug($this->slugger->slug($mineral->getName()));
                
            $mineral->addVariety($this->varieties[rand(0, count($this->varieties))]);

            $em->persist($mineral);
            $this->minerals[] = $mineral;
        }
    }

    private function addCategories(EntityManager $em)
    {
        for ($i=0; $i < 100; $i++) { 
            $category = new Category();
            $category->setName($this->faker->word())
                    ->setSlug($this->slugger->slug($category->getName()));
            
            $category->addMineral($this->minerals[rand(0, count($this->minerals))]);
            $em->persist($category);

        }
        
    }

}
