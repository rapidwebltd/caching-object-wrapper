<?php

class CountingValueGenerator
{
    private $calls = 0;

    public function generate($prefix = 'value')
    {
        ++$this->calls;

        return $prefix.'-'.$this->calls;
    }
}
