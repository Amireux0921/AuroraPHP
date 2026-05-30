<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-28 18:40:26
 * @LastEditTime: 2026-05-28 19:37:10
 */
namespace Aurora\Flash\AMF3;

/**
 * 此类型支持 Aurora 基础设施，不打算直接在您的代码中使用。
 * 
 * This type supports the Aurora infrastructure and is not intended to be used directly from your code.
 */
class ClassDefinition
{
    /**
     * @var string $ClassName 类名
     */
    public string $ClassName = '';
    /**
     * @var string[] $Members 类成员属性名
     */
    public array $Members = []; 
    /**
     * 指示该类是否可外部化。
     * 
     * Indicates whether the class is externalizable.
     * @var bool $Externalizable 
     */
    public bool $Externalizable = false;
    /**
     * 指示该类是否为动态的
     * 
     * Indicates whether the class is dynamic
     * @var bool $Dynamic 动态类
     */
    public bool $Dynamic = false;

    public function __construct(string $ClassName = '',array $Members = [],bool $Externalizable = false,bool $Dynamic = false)
    {
        $this->ClassName = $ClassName;
        $this->Members = $Members;
        $this->Externalizable = $Externalizable;
        $this->Dynamic = $Dynamic;
    }
    /**
     * 指示该类是否为类型化（非匿名）
     * 
     * Indicates whether the class is typed (not anonymous)
     */
    public function IsTypedObject():bool
    {
        return !empty($this->ClassName);
    }

    public function MemberCount():int
    {
        return count($this->Members);
    }
}