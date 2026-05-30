<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-28 19:35:46
 * @LastEditTime: 2026-05-30 02:19:11
 */
declare (strict_types = 1);
namespace Aurora\Flash\AMF3;

/**
 * IDataInput 接口提供了一组用于读取二进制数据的方法。
 * 
 * The IDataInput interface provides a set of methods for reading binary data.
 */
interface IDataInput
{
    /**
     * 从字节流或字节数组中读取一个布尔值。
     * 
     * Reads a Boolean from the byte stream or byte array. 
     */
    public function ReadBoolean():bool;
    /**
     * 从字节流或字节数组中读取一个有符号字节。
     * 
     * Reads a signed byte from the byte stream or byte array. 
     */
    public function ReadByte():int;
    /**
     * 从字节流或字节数组中读取长度为 length 的字节数据。
     * 
     * Reads length bytes of data from the byte stream or byte array. 
     */
    public function ReadBytes(int $length):string;
    /**
     * 从字节流或字节数组中读取一个 IEEE 754 双精度浮点数。
     * 
     * Reads an IEEE 754 double-precision floating point number from the byte stream or byte array. 
     */
    public function ReadDouble():float;
    /**
     * 从字节流或字节数组中读取一个 IEEE 754 单精度浮点数。
     * 
     *  Reads an IEEE 754 single-precision floating point number from the byte stream or byte array. 
     */
    public function ReadFloat():float;
    /**
     * 从字节流或字节数组中读取一个带符号的32位整数。
     * 
     * Reads a signed 32-bit integer from the byte stream or byte array. 
     */
    public function ReadInt():int;
    /**
     * 从字节流或字节数组中读取一个混合类型数据，编码为 AMF 序列化格式。
     * 
     *  Reads an Mixed from the byte stream or byte array, encoded in AMF serialized format. 
     */
    public function ReadMixed():mixed;
    /**
     * 从字节流或字节数组中读取有符号 16 位整数。
     * 
     * Reads a signed 16-bit integer from the byte stream or byte array.
     */
    public function ReadShort():int;
    /**
     * 从字节流或字节数组中读取一个无符号字节。
     * 
     * Reads an unsigned byte from the byte stream or byte array. 
     */
    public function ReadUnsignedByte():int;
    /**
     * 从字节流或字节数组中读取一个无符号的32位整数。
     * 
     * Reads an unsigned 32-bit integer from the byte stream or byte array. 
     */
    public function ReadUnsignedInt():int;
    /**
     * 从字节流或字节数组中读取一个无符号16位整数。
     * 
     * Reads an unsigned 16-bit integer from the byte stream or byte array. 
     */
    public function ReadUnsignedShort():int;
    /**
     * 从字节流或字节数组中读取 UTF-8 字符串。
     * 
     * Reads a UTF-8 string from the byte stream or byte array. 
     */
    public function ReadUTF():string;
    /**
     * 从字节流或字节数组中读取长度为 UTF-8 的字节序列，并返回一个字符串。
     * 
     * Reads a sequence of length UTF-8 bytes from the byte stream or byte array, and returns a string.
     */
    public function ReadUTFBytes(int $length):string;
}