<?php
namespace Aurora\Flash\AMF3;

/**
 * IDataOutput 接口提供了一组用于写入二进制数据的方法。
 * 
 * The IDataOutput interface provides a set of methods for writing binary data. 
 */
interface IDataOutput
{
    /**
     * 写入布尔值
     *  
     * Writes a Boolean value
     */
    public function WriteBoolean(bool $value):void;
    /**
     * 写入一个字节
     * 
     * Writes a byte.
     */
    public function WriteByte(int $value):void;
    /**
     * 将指定字节数组 bytes 中从偏移量（从零开始的索引）开始的 length 字节序列写入字节流。
     * 
     * Writes a sequence of length bytes from the specified byte array, bytes, starting offset(zero-based index) bytes into the byte stream.
     */
    public function WriteBytes(string $bytes):void;

    /**
     * 写入一个 IEEE 754 双精度（64 位）浮点数
     * 
     * Writes an IEEE 754 double-precision (64-bit) floating point number.
     */
    public function WriteDouble(float $value):void;
    /**
     * 写入一个 IEEE 754 单精度（32 位）浮点数。
     * 
     * Writes an IEEE 754 single-precision (32-bit) floating point number.
     */
    public function WriteFloat(float $value):void;
    /**
     * 写入一个32位有符号整数。
     * 
     * Writes a 32-bit signed integer.
     */
    public function WriteInt(int $value):void;
    /**
     * 将混合类型数据以 AMF 序列化格式写入字节流或字节数组。
     * 
     * Writes an object to the byte stream or byte array in AMF serialized format.
     */
    public function WriteMixed(mixed $value):void;
    /**
     * 写一个16位整数
     * 
     * Writes a 16-bit integer
     */
    public function WriteShort(int $value):void;
    /**
     * 写入一个32位无符号整数。
     * 
     * Writes a 32-bit unsigned integer.
     */
    public function WriteUnsignedInt(int $value):void;
    /**
     * 将 UTF-8 字符串写入字节流。
     * 
     * Writes a UTF-8 string to the byte stream.
     */
    public function WriteUTF(string $value):void;
    /**
     * 写入一个 UTF-8 字符串。
     * 
     * Writes a UTF-8 string.
     */
    public function WriteUTFBytes(string $value):void;
}