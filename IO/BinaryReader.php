<?php
namespace Aurora\IO;

class BinaryReader
{
    private Stream $Stream;

    public function __construct(Stream &$Stream)
    {
        $this->Stream = $Stream;
    }

    /**
     * 读取 1字节 无符号字节 byte
     */
    public function ReadByte(): int
    {
        $bin = $this->ReadBytes(1);
        return unpack('C', $bin)[1];
    }

    /**
     * 读取 1字节 有符号字节 sbyte
     */
    public function ReadSByte(): int
    {
        $bin = $this->ReadBytes(1);
        return unpack('c', $bin)[1];
    }

    /**
     * 读取 2字节 有符号短整型 short（大端）
     */
    public function ReadShort(): int
    {
        $bin = $this->ReadBytes(2);
        return unpack('S', $bin)[1];
    }

    /**
     * 读取 2字节 无符号短整型 ushort（大端）
     */
    public function ReadUShort(): int
    {
        $bin = $this->ReadBytes(2);
        return unpack('n', $bin)[1];
    }

    /**
     * 读取 4字节 有符号整型 int（大端）
     */
    public function ReadInt(): int
    {
        $bin = $this->ReadBytes(4);
        return unpack('L', $bin)[1];
    }

    /**
     * 读取 4字节 无符号整型 uint（大端）
     */
    public function ReadUInt(): int
    {
        $bin = $this->ReadBytes(4);
        return unpack('N', $bin)[1];
    }

    /**
     * 读取 8字节 有符号长整型 long（大端）
     */
    public function ReadLong(): int
    {
        $bin = $this->ReadBytes(8);
        return unpack('Q', $bin)[1];
    }

    /**
     * 读取 8字节 无符号长整型 ulong（大端）
     */
    public function ReadULong(): int
    {
        $bin = $this->ReadBytes(8);
        return unpack('P', $bin)[1];
    }

    /**
     * 读取布尔值(1字节: 0=false, 非0=true)
     */
    public function ReadBoolean(): bool
    {
        return $this->ReadByte() !== 0;
    }

    /**
     * 读取 4字节 单精度浮点数 float
     */
    public function ReadFloat(): float
    {
        $bin = $this->ReadBytes(4);
        return unpack('f', $bin)[1];
    }

    /**
     * 读取 8字节 双精度浮点数 double
     */
    public function ReadDouble(): float
    {
        $bin = $this->ReadBytes(8);
        $bin = strrev($bin);
        return unpack('d', $bin)[1];
    }

    /**
     * 读取原始字节，自动移动指针
     */
    public function ReadBytes(int $length): string
    {
        // $bin = $this->Stream->Read($length);
        // $hex = bin2hex($bin);
        
        // $byteArr = [];
        // $len = strlen($bin);
        // for ($i = 0; $i < $len; $i++) {
        //     $byteArr[] = ord($bin[$i]);
        // }
        // return $bin;
        return $this->Stream->Read($length);
    }

    public function ReadUTF(int $length): string
    {
        return mb_convert_encoding($this->ReadBytes($length),'UTF-8', 'UTF-8');
    }
}