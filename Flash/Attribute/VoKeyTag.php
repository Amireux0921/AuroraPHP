<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-28 21:54:06
 * @LastEditTime: 2026-05-29 22:50:08
 */
namespace Aurora\Flash\Attribute;
use Attribute;
/**
 * Vo对象序属性名注解
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class VoKeyTag
{
    public string $Name;
    /**
     * 构造函数
     * @param string $Name 自定义属性名 | 不填序则默认属性名 等同于没打#[VoKeyTag()]
     */
    public function __construct(string $Name = '')
    {
        $this->Name = $Name;
    }
}
