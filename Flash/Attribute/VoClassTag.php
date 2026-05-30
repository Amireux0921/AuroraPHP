<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 22:49:49
 * @LastEditTime: 2026-05-30 13:57:19
 */
namespace Aurora\Flash\Attribute;

use Attribute;
/**
 * Vo序列化对象注解 用来定义自定义类名，或默认序列化类名方式
 */
#[Attribute(Attribute::TARGET_CLASS)]
class VoClassTag
{
    public bool|string $ClassName;
    /**
     * 构造函数
     * @param bool|string $ClassName 类名 | true = 自动序列化命名空间类名/ false or "" = 不显式序列化类名,以匿名对象序列化 | 输入字符串可显式序列化指定类名
     */
    public function __construct(bool|string $ClassName = true)
    {
        $this->ClassName = $ClassName;
    }
}