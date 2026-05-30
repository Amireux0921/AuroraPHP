<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Date: 2026-05-28 18:48:16
 * @LastEditTime: 2026-05-28 23:44:08
 */
namespace Aurora\Flash;


enum AMF0TypeCode:int
{
    case Number = 0;

    case Boolean = 1;

    case String = 2;

    case ASObject = 3;

    case Null = 5;

    case Undefined = 6;

    case Reference = 7;

    case AssociativeArray = 8;

    case EndOfObject = 9;

    case Array = 10;

    case DateTime = 11;

    case LongString = 12;

    case Xml = 15;

    case CustomClass = 16;

    case AMF3Tag = 17;
}