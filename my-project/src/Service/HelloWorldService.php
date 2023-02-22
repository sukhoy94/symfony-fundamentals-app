<?php

declare(strict_types=1);


namespace App\Service;


class HelloWorldService
{
    private RandomNumberGenerator $randomNumberGenerator;
    private string $name;
    
    public function __construct(RandomNumberGenerator $randomNumberGenerator, string $name)
    {
        $this->randomNumberGenerator = $randomNumberGenerator;
        $this->name = $name;
    }
    
    public function sayHello(): void
    {
        echo "Hello World {$this->name}";        
        echo $this->randomNumberGenerator->generate();
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}