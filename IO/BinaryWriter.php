<?php
namespace Aurora\IO;

class BinaryWriter
{
    private Stream $Stream;

    public function __construct(Stream &$Stream)
    {
        $this->Stream = $Stream;
    }

    /**
     * 写入原始字节
     * @param string $data
     * @return void
     */
    public function WriteRaw(string $data): void
    {
        $this->Stream->write($data);
    }

    /**
     * 1字节 无符号 byte
     * @param int $value
     * @return void
     */
    public function WriteByte(int $value): void
    {
        $this->WriteRaw(pack('C', $value));
    }

    /**
     * 2字节 有符号 short（大端）
     * @param int $value
     * @return void
     */
    public function WriteShort(int $value): void
    {
        $this->WriteRaw(pack('n', $value));
    }

    /**
     * 4字节 有符号 int（大端）
     * @param int $value
     * @return void
     */
    public function WriteInt(int $value): void
    {
        $this->WriteRaw(pack('N', $value));
    }

    /**
     * 8字节 有符号 long（大端）
     * @param int $value
     * @return void
     */
    public function WriteLong(int $value): void
    {
        $this->WriteRaw(pack('J', $value));
    }

    /**
     * 1字节 布尔值
     * @param bool $value
     * @return void
     */
    public function WriteBool(bool $value): void
    {
        $this->WriteByte($value ? 1 : 0);
    }

    /**
     * 4字节 float
     * @param float $value
     * @return void
     */
    public function WriteFloat(float $value): void
    {
        $this->WriteRaw(strrev(pack('f', $value)));
    }

    /**
     * 8字节 double
     * @param float $value
     * @return void
     */
    public function WriteDouble(float $value): void
    {
        $this->WriteRaw(strrev(pack('d', $value)));
    }

    public function WriteUTF(string $value):void
    {
        $this->WriteRaw(mb_convert_encoding($value,'UTF-8'));
    }

    /**
     * 清空缓冲区与指针
     * @return void
     */
    public function Close(): void
    {
        $this->Stream->close();
    }

    public function ToArray(): string
    {
        return $this->Stream->toArray();
    }

}