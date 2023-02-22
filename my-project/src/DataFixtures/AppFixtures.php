<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    protected Generator $faker;
    protected ObjectManager $manager;
    
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;
        
        $this->loadStocks();
        $this->manager->flush();
    }
    
    private function generateStockSymbolFromTitle(string $title): string
    {        
        $titleOnlyWords = str_replace(
            [
                ',',
                '.',
                '-',
            ], 
            [
                '',
                '',
                ' ',
            ], 
            $title
        );
    
        $words = explode(" ", $titleOnlyWords);
        $acronym = "";
    
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }
        
        return $acronym;
    }
    
    private function loadStocks(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $stock = new Stock();
        
            $stock->setTitle($this->faker->company);
            $stock->setSymbol($this->generateStockSymbolFromTitle((string) $stock->getTitle()));
            $stock->setPrice($this->faker->randomNumber(3));
        
            $this->manager->persist($stock);
        }
    }
}
