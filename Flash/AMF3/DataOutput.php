<?php
namespace Aurora\Flash\AMF3;

use Aurora\Flash\IO\AMFWriter;
use Aurora\Flash\ObjectEncoding;

class DataOutput implements IDataOutput
{
    private AMFWriter $AMFWriter;

    private ObjectEncoding $encoding = ObjectEncoding::AMF3;

    public function __construct(AMFWriter $AMFWriter)
    {
        $this->AMFWriter = $AMFWriter;
    }
    public function WriteBoolean(bool $value) : void
    {
        $this->AMFWriter->WriteBool($value);
    }
    public function WriteByte(int $value):void
    {
        $this->AMFWriter->WriteByte($value);
    }
    public function WriteBytes(string $bytes):void
    {
        $this->AMFWriter->WriteRaw($bytes);
    }
    public function WriteDouble(float $value) :void
    {
        $this->AMFWriter->WriteDouble($value);
    }
    public function WriteFloat(float $value):void
    {
        $this->AMFWriter->WriteFloat($value);
    }
    public function WriteInt(int $value):void
    {
        $this->AMFWriter->WriteInt($value);
    }
    public function WriteMixed(mixed $value):void
    {
        if($this->encoding == ObjectEncoding::AMF3)
        {
            $this->AMFWriter->WriteAMF3($value);
        }
        else
        {
            $this->AMFWriter->WriteAMF0($value);
        }
    }
    public function WriteShort(int $value):void
    {
        $this->AMFWriter->WriteShort($value);
    }
    public function WriteUnsignedInt(int $value):void
    {
        $this->AMFWriter->WriteInt($value);
    }
    public function WriteUTF(string $value):void
    {
        $this->AMFWriter->WriteUTF($value);
    }
    public function WriteUTFBytes(string $value):void
    {
        $this->AMFWriter->WriteUTF($value);
    }
}