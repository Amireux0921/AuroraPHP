<?php
namespace Aurora\Flash\IO;


class AMFBody
{
    public string $Target;

    public string $Response;

    public mixed $Content;

    public function __construct(string $Target = '',string $Response = '',mixed $Content = null)
    {
        $this->Target = $Target;
        $this->Response = $Response;
        $this->Content = $Content;
    }
}