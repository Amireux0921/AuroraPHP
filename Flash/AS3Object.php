<?php
/*
 * @Description:
 * @Version: 1.0.0
 * @Author: Lonely
 * @Date: 2026-05-27 20:45:41
 * @LastEditors: Please set LastEditors
 * @LastEditTime: 2026-05-30 19:53:48
 */
namespace Aurora\Flash;

use Aurora\Flash\AMF3\ClassDefinition;
use Aurora\Flash\AMF3\IExternalizable;
use Aurora\Flash\Attribute\VoClassTag;
use Aurora\Flash\Attribute\VoKeyTag;
use Aurora\Flash\Exceptions\UnexpectedAMF;
use stdClass;
use ReflectionClass;
use ReflectionProperty;

class AS3Object
{
    public ClassDefinition $Trait;

    public array $Values = [];

    public array $DynamicMembersAndValues  = [];

    public function __construct()
    {
        $this->Trait = new ClassDefinition();
    }

    /**
     * 将当前AS对象是否设置为动态对象
     * @param bool $value
     * @return void
     */
    public function IsDynamicObject(bool $value):void
    {
        $this->Trait->Dynamic = $value;
    }

    /**
     * 将AS对象转换为实体对象 因为AS对象本身支持访问器魔术方法，不推荐转换
     * 
     * 如果类不存在 则抛异常
     * 
     * 如果类有危险魔术方法 会造成反序列化漏洞
     * @return object
     */
    public function ToObject():object
    {
        if($this->Trait->Dynamic || empty($this->Trait->ClassName))
        {
            return $this;
        }
        else
        {
            $Reflection = null;

            if(array_key_exists($this->Trait->ClassName,Config::$Mapping))
            {

                $Name = Config::$Mapping[$this->Trait->ClassName];

                if(!class_exists($Name)) throw new UnexpectedAMF("Cannot find the ".$Name." class");
                /**
                 * @var object $Class
                 */
                $Reflection = new ReflectionClass($Name);
            }
            else
            {
                $Name = $this->Trait->ClassName;

                if(!class_exists($Name)) throw new UnexpectedAMF("Cannot find the ".$Name." class");
                /**
                 * @var object $Class
                 */
                $Reflection = new ReflectionClass(str_replace(".","\\",$Name));
            }

            $instance  = $Reflection->newInstance();

            $publicProps = $Reflection->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($publicProps as $prop)
            {
                $name = $prop->getName();

                if(in_array($name,$this->Trait->Members))
                {
                    // 数据源存在该字段才赋值
                    if (array_key_exists($name, $this->Trait->Members))
                    {
                        $prop->setValue($instance, $this->Values[array_search($name,$this->Trait->Members,true)] );
                    }
                }
            }

            return $instance;

        }
    }
    /**
     * 负责将PHP对象封装成AS3对象
     */
    public function FromObject(mixed $instance): void
    {
        if($instance instanceof stdClass)
        {
            $this->Trait->Dynamic = true;
            $this->DynamicMembersAndValues = (array)$instance;
        }
        else
        {
            if($instance instanceof IExternalizable)
            {
                $this->Trait->Externalizable = true;
            }
            else
            {
                $Reflection = new ReflectionClass($instance);
                $attrs = $Reflection->getAttributes(VoClassTag::class);

                if(!empty($attrs))
                {
                    /**
                     * @var VoClassTag $VoTag
                     */
                    $VoTag = $attrs[0]->newInstance();

                    if (is_bool($VoTag->ClassName) && $VoTag->ClassName === true) 
                    {
                        $this->Trait->ClassName = $Reflection->getName();
                    } 
                    else 
                    {
                        $this->Trait->ClassName = (string)$VoTag->ClassName;
                    }
                }

                foreach($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
                {
                    $KeyTag = $property->getAttributes(VoKeyTag::class);

                    if(!empty($KeyTag))
                    {
                        $propertyName = $KeyTag[0]->newInstance()->Name;

                        if(empty($propertyName))
                        {
                            $this->Trait->Members[] = $property->getName();
                        }
                        else
                        {
                            $this->Trait->Members[] = $propertyName;
                        }
                    }
                    else
                    {
                        $this->Trait->Members[] = $property->getName();
                    }

                    $this->Values[] = $property->getValue($instance);
                }
            }
        }
    }

    public function __get(string $name): mixed
    {
        if($this->Trait->Dynamic)
        {
            return $this->DynamicMembersAndValues[$name] ?? null;
        }
        else
        {
            $index = array_search($name, $this->Trait->Members);
            if($index === false) return null;
            return $this->Values[$index];
        }
    }

    public function __set(string $name, mixed $value): void
    {
        if($this->Trait->Dynamic)
        {
            $this->DynamicMembersAndValues[$name] = $value;
        }
        else
        {
            $this->Trait->Members[] = $name;
            $this->Values[] = $value;
        }
    }

    public function __isset(string $name): bool
    {
        if($this->Trait->Dynamic)
        {
            return isset($this->DynamicMembersAndValues[$name]);
        }
        else
        {
            $index = array_search($name, $this->Trait->Members);
            if($index === false) return false;
            return isset($this->Values[$index]);
        }
    }

    public function __unset(string $name): void
    {
        if($this->Trait->Dynamic)
        {
            unset($this->DynamicMembersAndValues[$name]);
        }
        else
        {
            $index = array_search($name, $this->Trait->Members);
            if($index === false) return;
            unset($this->Values[$index]);
        }
    }
}

