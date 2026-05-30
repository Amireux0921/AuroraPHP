<?php
namespace Aurora\Flash;

use Aurora\Flash\AMF3\ObjectProxy;
use Aurora\Flash\Attribute\VoClassTag;
use Aurora\Flash\Attribute\VoKeyTag;
use Exception;
use stdClass;
use ReflectionClass;
use ReflectionProperty;

class ASObject
{
    public string $ClassName = '';
    public bool $IsAnonymous = false;
    public array $DynamicMembersAndValues = [];

    public function FromObject(object $instance): void
    {

        if ($instance instanceof stdClass) {
            $this->DynamicMembersAndValues = (array)$instance;
        } else {

            $Reflection = new ReflectionClass($instance);

            $attrs = $Reflection->getAttributes(VoClassTag::class);

            if (!empty($attrs)) {
                /** @var VoClassTag $VoTag */
                $VoTag = $attrs[0]->newInstance();
                if (is_bool($VoTag->ClassName) && $VoTag->ClassName === true) {
                    $this->ClassName = $Reflection->getName();
                } else {
                    $this->ClassName = (string)$VoTag->ClassName;
                }
            }

            foreach ($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $KeyTag = $property->getAttributes(VoKeyTag::class);
                if (!empty($KeyTag)) {
                    $propertyName = $KeyTag[0]->newInstance()->Name;
                    if (empty($propertyName)) {
                        $this->DynamicMembersAndValues[$property->getName()] = $property->getValue($instance);
                    } else {
                        $this->DynamicMembersAndValues[$propertyName] = $property->getValue($instance);
                    }
                } else {
                    $this->DynamicMembersAndValues[$property->getName()] = $property->getValue($instance);
                }
            }
        }

        $this->IsAnonymous = empty($this->ClassName);
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
        if(empty($this->ClassName))
        {
            return $this;
        }
        else
        {
            $instance = null;

            if(array_key_exists ($this->ClassName,Config::$Mapping))
            {
                /**
                 * @var object $Class
                 */
                $Class = new ReflectionClass(Config::$Mapping[$this->ClassName]);
                
                $instance  = $Class->newInstance();

            }
            else
            {
                /**
                 * @var object $Class
                 */
                $Class = new ReflectionClass(str_replace(".","\\",$this->ClassName));

                $instance  = $Class->newInstance();
            }

            $publicProps = $instance ->getProperties(ReflectionProperty::IS_PUBLIC);

            $publicProps->setAccessible(true);

            foreach ($publicProps as $prop)
            {
                $name = $prop->getName();

                if(in_array($name,$this->DynamicMembersAndValues))
                {
                    // 数据源存在该字段才赋值
                    if (array_key_exists($name, $this->DynamicMembersAndValues[$name]))
                    {
                        $prop->setValue($instance, $this->DynamicMembersAndValues[$name]);
                    }
                }
            }

            return $instance;
        }
    }

    public function __get(string $name): mixed
    {
        return $this->DynamicMembersAndValues[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->DynamicMembersAndValues[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->DynamicMembersAndValues[$name]);
    }

    public function __unset(string $name): void
    {
        unset($this->DynamicMembersAndValues[$name]);
    }
}