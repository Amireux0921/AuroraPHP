<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-28 19:42:15
 * @LastEditTime: 2026-05-29 22:46:47
 */
namespace Aurora\Flash;


/**
 * 对象编码（AMF 版本）
 * 
 * Object encoding (AMF version).
 */
enum ObjectEncoding:int
{
    /**
     * AMF0 序列化
     * 
     * AMF0 serialization.
     */
    case AMF0 = 0;
    /**
     * AMF3 序列化
     * 
     * AMF3 serialization.
     */
    case AMF3 = 3;
}