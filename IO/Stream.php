<?php
namespace Aurora\IO;



abstract class Stream
{
    protected string $Buffer;
    protected int $offset = 0;
    public abstract function Write(string $data):void;

    public abstract function Read(int $length):string;

    public abstract function Seek(int $offset): bool;

    public abstract function Flush() :void;

    public abstract function Close() :void;
    public function ToArray():string
    {
        return $this->Buffer;
    }
}