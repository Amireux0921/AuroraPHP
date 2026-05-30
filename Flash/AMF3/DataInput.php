<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 21:08:37
 * @LastEditTime: 2026-05-30 02:23:51
 */
declare (strict_types = 1);
namespace Aurora\Flash\AMF3;

use Aurora\Flash\IO\AMFReader;
use Aurora\Flash\ObjectEncoding;
use Override;

class DataInput implements IDataInput
{
    private AMFReader $_amfReader;

    private ObjectEncoding $_objectEncoding;

    public function __construct(AMFReader $amfReader)
    {
        $this->_amfReader = $amfReader;
        $this->_objectEncoding = ObjectEncoding::AMF3;
    }
    public function ReadBoolean():bool
    {
        return $this->_amfReader->ReadBoolean();
    }

    public function ReadByte():int
    {
        return $this->_amfReader->ReadByte();
    }

    public function ReadBytes(int $length):string
    {
        return $this->_amfReader->ReadBytes($length);

    }

    public function ReadDouble():float
    {
        return $this->_amfReader->ReadDouble();
    }

    public function ReadFloat():float
    {
        return $this->_amfReader->ReadFloat();
    }

    public function ReadInt():int
    {
        return $this->_amfReader->ReadInt();
    }

    public function ReadMixed():mixed
    {
        if($this->_objectEncoding === ObjectEncoding::AMF0)
        {
            return $this->_amfReader->ReadAMF0();
        }
        else
        {
            return $this->_amfReader->ReadAMF3();
        }

    }

    public function ReadShort():int
    {
        return $this->_amfReader->ReadShort();
    }

    public function ReadUnsignedByte():int
    {
        return $this->_amfReader->ReadSByte();

    }

    public function ReadUnsignedInt():int
    {
        return $this->_amfReader->ReadUInt();
    }

    public function ReadUnsignedShort():int
    {
        return $this->_amfReader->ReadUShort();
    }

    public function ReadUTF():string
    {
        return $this->_amfReader->ReadString();
    }

    public function ReadUTFBytes(int $length):string
    {
        return $this->_amfReader->ReadUTF($length);
    }

}