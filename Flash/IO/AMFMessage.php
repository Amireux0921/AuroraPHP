<?php
namespace Aurora\Flash\IO;

use Aurora\Flash\ObjectEncoding;

class AMFMessage
{
    public ObjectEncoding $Version;

    public array $Headers = [];

    public array $Bodys = [];

    public function __construct(ObjectEncoding $Version = ObjectEncoding::AMF3)
    {
        $this->Version = $Version;
    }

    public function AddBody(AMFBody $Body)
    {
        $this->Bodys[] = $Body;
    }

    public function AddHeader(AMFHeader $Header)
    {
        $this->Headers[] = $Header;
    }
}

