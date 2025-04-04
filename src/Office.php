<?php

namespace PhpTwinfield;

class Office
{
    /**
     * @var string The code of the office.
     */
    private $code;

    /**
     * @var string The code of the country of the office.
     */
    private $countryCode;

    /**
     * @var string The name of the office.
     */
    private $name;

    public static function fromCode(string $code) {
        $instance = new self;
        $instance->setCode($code);

        return $instance;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        
        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        
        return $this;
    }

    public function __toString()
    {
        return $this->getCode();
    }
}
