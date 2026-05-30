<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Date: 2026-05-28 18:48:16
 * @LastEditTime: 2026-05-29 23:47:59
 */
namespace Aurora\Flash;


enum AMF3TypeCode:int
{
    case Undefined = 0;

    case Null = 1;

    case BooleanFalse = 2;

    case BooleanTrue = 3;

    case Integer = 4;

    case Number = 5;

    case String = 6;

    case Xml2 = 7;

    case DateTime = 8;

    case Array = 9;

    case Object = 10;

    case Xml = 11;

    case ByteArray = 12;

    case IntVector = 13; //不受支持 走Array序列化

    case UIntVector = 14; //不受支持 走Array序列化

    case NumberVector = 15; //不受支持 走Array序列化

    case ObjectVector = 16; //不受支持 走Array序列化

    case Dictionary = 17; //不受支持 不进行序列化
}