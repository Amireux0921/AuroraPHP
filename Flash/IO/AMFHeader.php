<?php
namespace Aurora\Flash\IO;


class AMFHeader
{
    public string $Name;

    public bool $MustUnderstand;

    public mixed $Content;

    public function __construct(string $Name = '',bool $MustUnderstand = false, mixed $Content = null)
    {
        $this->Name = $Name;
        $this->MustUnderstand = $MustUnderstand;
        $this->Content = $Content;
    }
}