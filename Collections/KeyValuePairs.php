<?php
namespace Aurora\Collections;


class KeyValuePairs
{
    public mixed $Key;

    public mixed $Value;
    
    public function __construct(mixed $key,mixed $value)
    {
        $this->Key = $key;
        $this->Value = $value;
    }
}