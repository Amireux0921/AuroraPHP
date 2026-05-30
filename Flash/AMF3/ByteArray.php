<?php
declare (strict_types = 1);

namespace Aurora\Flash\AMF3;

use Aurora\Flash\IO\AMFReader;
use Aurora\Flash\IO\AMFWriter;
use Aurora\Flash\ObjectEncoding;
use Aurora\IO\MemoryStream;
use Aurora\IO\Stream;

/**
 * Flex ByteArray。ByteArray 类提供方法和属性，用于优化读取、写入和处理二进制数据。
 * 
 * Flex ByteArray. The ByteArray class provides methods and properties to optimize reading, writing, and working with binary data.
 */
class ByteArray implements IDataInput , IDataOutput
{
    private Stream $Stream;

    private DataInput $_datainput;

    private DataOutput $_dataoutput;

    private ObjectEncoding $_objectEncoding;


    public function __construct(string $Buffer = '')
    {
        $this->Stream = new MemoryStream();
        $this->Stream->Write($Buffer);

        $amfReader = new AMFReader($this->Stream);
        $amfWriter = new AMFWriter($this->Stream);

        $this->_datainput = new DataInput($amfReader);
        $this->_dataoutput = new DataOutput($amfWriter);
        $this->_objectEncoding = ObjectEncoding::AMF3;
    }

    public function ReadBoolean():bool
    {
        return $this->_datainput->ReadBoolean();
    }

    public function ReadByte():int
    {
        return $this->_datainput->ReadByte();
    }

    public function ReadBytes(int $length):string
    {
        return $this->_datainput->ReadBytes($length);
    }

    public function ReadDouble():float
    {
        return $this->_datainput->ReadDouble();
    }

    public function ReadFloat():float
    {
        return $this->_datainput->ReadFloat();
    }

    public function ReadInt():int
    {
        return $this->_datainput->ReadInt();
    }

    public function ReadMixed():mixed
    {
        return $this->_datainput->ReadMixed();
    }

    public function ReadShort():int
    {
        return $this->_datainput->ReadShort();
    }

    public function ReadUnsignedInt():int
    {
        return $this->_datainput->ReadUnsignedInt();
    }

    public function ReadUnsignedByte():int
    {
        return $this->_datainput->ReadUnsignedByte();
    }

    public function ReadUnsignedShort():int
    {
        return $this->_datainput->ReadUnsignedShort();
    }

    public function ReadUTF():string
    {
        return $this->_datainput->ReadUTF();
    }

    public function ReadUTFBytes(int $length):string
    {
        return $this->_datainput->ReadUTFBytes($length);
    }

    public function WriteBoolean(bool $value):void
    {
        $this->_dataoutput->WriteBoolean($value);
    }

    public function WriteByte(int $value):void
    {
        $this->_dataoutput->WriteByte($value);
    }

    public function WriteBytes(string $bytes):void
    {
        $this->_dataoutput->WriteBytes($bytes);
    }

    public function WriteDouble(float $value):void
    {
        $this->_dataoutput->WriteDouble($value);
    }

    public function WriteFloat(float $value):void
    {
        $this->_dataoutput->WriteFloat($value);
    }

    public function WriteInt(int $value):void
    {
        $this->_dataoutput->WriteInt($value);
    }

    public function WriteMixed(mixed $value):void
    {
        $this->_dataoutput->WriteMixed($value);
    }

    public function WriteShort(int $value):void
    {
        $this->_dataoutput->WriteShort($value);
    }

    public function WriteUnsignedInt(int $value):void
    {
        $this->_dataoutput->WriteUnsignedInt($value);
    }

    public function WriteUTF(string $value):void
    {
        $this->_dataoutput->WriteUTF($value);
    }

    public function WriteUTFBytes(string $value):void
    {
        $this->_dataoutput->WriteUTFBytes($value);
    }

    public function ToArray():string
    {
        return $this->Stream->ToArray();
    }
}