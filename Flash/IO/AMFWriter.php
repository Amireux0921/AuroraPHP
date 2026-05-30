<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 23:46:40
 * @LastEditTime: 2026-05-30 12:04:07
 */
declare (strict_types = 1);
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Date: 2026-05-28 18:14:48
 * @LastEditTime: 2026-05-29 22:13:14
 */
namespace Aurora\Flash\IO;

use Aurora\Collections\Generic\IDictionary;
use Aurora\Collections\KeyValuePairs;
use Aurora\Flash\Exceptions\UnexpectedAMF;
use Aurora\Flash\AMF3\IExternalizable;
use Aurora\Flash\AMF3\DataOutput;
use Aurora\Flash\AMF3\ByteArray;
use Aurora\Flash\AMF0TypeCode;
use Aurora\Flash\AMF3TypeCode;
use Aurora\IO\BinaryWriter;
use Aurora\Flash\AS3Object;
use Aurora\Flash\ASObject;
use DOMDocument;
use DateTimeZone;
use DateTime;

class AMFWriter extends BinaryWriter
{
    protected array $AMF3StringReferences = [];

    protected array $AMF3ObjectReferences = [];

    protected array $AMF3TraitsReferences = [];

    protected array $AMF0ObjectReferences = [];

    /**
     * 重置全部引用池
     */
    public function ResetReferences()
    {
        $this->AMF3StringReferences = [];
        $this->AMF3ObjectReferences = [];
        $this->AMF3TraitsReferences = [];

        $this->AMF0ObjectReferences = [];
    }

    /**
     * 添加到AMF0对象引用池
     */
    public function AddReference(mixed $value)
    {
        $this->AMF0ObjectReferences[] = $value;
        
    }

    /**
     * 写AMF0对象引用指针
     */
    public function WriteReference(mixed $value)
    {
        $index = array_search($value, $this->AMF0ObjectReferences, true);
        if($index === false) throw new UnexpectedAMF("Reference not found");
        $this->WriteShort($index);
    }

    public function WriteDateTime(DateTime $value)
    {
        $epoch = new DateTime('1970-01-01', new DateTimeZone('UTC'));

        $utcTime = (clone $value)->setTimezone(new DateTimeZone('UTC'));

        $diffSec = $utcTime->getTimestamp() - $epoch->getTimestamp();

        $ms = $diffSec * 1000 + (int)($utcTime->format('u') / 1000);

        $this->WriteDouble((float)$ms);

        $offsetMin = (int)($value->getOffset() / 60);

        $this->WriteShort($offsetMin);
    }

    public function WriteXmlDocument(DOMDocument $value)
    {
        $this->WriteLongString($value->saveXml());
    }

    /**
     * 关联数组写入 对标字典 Dictionary<string,object>
     * @param array<string,mixed> $value Dictionary<string,mixed>
     */
    public function WriteAssociativeArray(array $value)
    {
        $this->WriteInt(count($value));

        foreach($value as $key => $var)
        {
            $this->WriteString((string)$key);
            $this->WriteAMF0($var);
        }
        $this->WriteEndMarkup();
    }

    public function WriteEndMarkup()
    {
        $this->WriteShort(0);
        $this->WriteByte(AMF0TypeCode::EndOfObject->value);
    }

    /**
     * 
     * @param mixed[] $value 
     */
    public function WriteArray(array $value)
    {
        $this->WriteInt(count($value));
        foreach($value as $key)
        {
            $this->WriteAMF0($key);
        }
    }

    

    public function WriteAMF0(mixed $value)
    {
        if(is_null($value))
        {
            $this->WriteByte(AMF0TypeCode::Null->value);
        }
        else if (is_string($value))
        {
            if(strlen($value) > 65535)
            {
                $this->WriteByte(AMF0TypeCode::LongString->value);
                $this->WriteLongString($value);
            }
            else
            {
                $this->WriteByte(AMF0TypeCode::String->value);
                $this->WriteString($value);
            }
        }
        else if (is_integer($value) || is_int($value) || is_long($value) || is_double($value) || is_float($value))
        {
            $this->WriteByte(AMF0TypeCode::Number->value);
            $this->WriteDouble($value);
        }
        else if (is_bool($value))
        {
            $this->WriteByte(AMF0TypeCode::Boolean->value);
            $this->WriteBool($value);
        }
        
        else if ($value instanceof DateTime)
        {
            $this->WriteByte(AMF0TypeCode::DateTime->value);
            $this->WriteDateTime($value);
        }
        else if ($value instanceof DOMDocument)
        {
            $this->WriteByte(AMF0TypeCode::Xml->value);
            $this->WriteXmlDocument($value);
        }
        else if (is_array($value))
        {
            $refIndex = array_search($value, $this->AMF0ObjectReferences, true);
            if ($refIndex !== false)
            {
                $this->WriteByte(AMF0TypeCode::Reference->value);
                $this->WriteReference($value);
            }
            else
            {
                $this->AddReference($value);
                if(array_is_list($value))
                {
                    $this->WriteByte(AMF0TypeCode::Array->value);
                    $this->WriteArray($value);
                }
                else
                {
                    $this->WriteByte(AMF0TypeCode::AssociativeArray->value);
                    $this->WriteAssociativeArray($value);
                }
            }
        }
        else if ($value instanceof ASObject)
        {
            $refIndex = array_search($value, $this->AMF0ObjectReferences, true);
            if ($refIndex !== false)
            {
                $this->WriteByte(AMF0TypeCode::Reference->value);
                $this->WriteReference($value);
            }
            else
            {
                $this->AddReference($value);
                if($value->IsAnonymous)
                {
                    $this->WriteByte(AMF0TypeCode::ASObject->value);
                    $this->WriteObject($value);
                }
                else
                {
                    $this->WriteByte(AMF0TypeCode::CustomClass->value);
                    $this->WriteTypeObject($value);
                }
            }
        }
        else
        {
            $this->WriteByte(AMF0TypeCode::AMF3Tag->value);
            $this->WriteAMF3($value);
        }
    }

    public function WriteTypeObject(ASObject $value)
    {
        $this->WriteString($value->ClassName);
        
        foreach($value->DynamicMembersAndValues as $key => $var)
        {
            $this->WriteString($key);
            $this->WriteAMF0($var);
        }

        $this->WriteEndMarkup();
    }
    
    /**
     * 匿名对象
     */
    public function WriteObject(ASObject $value)
    {
        foreach($value->DynamicMembersAndValues as $key => $var)
        {
            $this->WriteString($key);
            $this->WriteAMF0($var);
        }

        $this->WriteEndMarkup();
    }

    public function WriteString(string $text): void
    {
        $length = strlen($text);
        // 先写入长度，再写入内容
        $this->WriteShort($length);
        $this->WriteUTF($text);
    }

    public function WriteLongString(string $text):void
    {
        $length = strlen($text);
        // 先写入长度，再写入内容
        $this->WriteInt($length);
        $this->WriteUTF($text);
    }

    public function WriterUInt29(int $value)
    {
        $value &= 0x1FFFFFFF;
        if ($value < 0x80) {
            $this->WriteByte($value);
        } elseif ($value < 0x4000) {
            $this->WriteByte((($value >> 7) & 0x7F) | 0x80);
            $this->WriteByte($value & 0x7F);
        } elseif ($value < 0x200000) {
            $this->WriteByte((($value >> 14) & 0x7F) | 0x80);
            $this->WriteByte((($value >> 7) & 0x7F) | 0x80);
            $this->WriteByte($value & 0x7F);
        } else {
            $this->WriteByte((($value >> 22) & 0x7F) | 0x80);
            $this->WriteByte((($value >> 15) & 0x7F) | 0x80);
            $this->WriteByte((($value >> 8) & 0x7F) | 0x80);
            $this->WriteByte($value & 0xFF);
        }
    }

    public function WriteAMF3(mixed $value)
    {
        if(is_null($value)) $this->WriteByte(AMF3TypeCode::Null->value);
        else if (is_bool($value))
        {
            $this->WriteByte($value ? AMF3TypeCode::BooleanTrue->value : AMF3TypeCode::BooleanFalse->value);
        }
        else if (is_int($value) || is_integer($value) || is_long($value))
        {
            if($value < -268435456 || $value > 268435455)
            {
                $this->WriteByte(AMF3TypeCode::Number->value);
                $this->WriteDouble($value);
            }
            else 
            {
                $this->WriteByte(AMF3TypeCode::Integer->value);
                $this->WriterUInt29($value);
            }
        }
        else if (is_float($value) || is_double($value))
        {
            $this->WriteByte(AMF3TypeCode::Integer->value);
            $this->WriteDouble($value);
        }
        else if (is_string($value))
        {
            $this->WriteByte(AMF3TypeCode::String->value);
            $this->WriterAMF3String($value);
        }
        else if (is_array($value))
        {
            $this->WriteByte(AMF3TypeCode::Array->value);
            if(array_is_list($value))
            {
                $this->WriteAMF3Array($value);
            }
            else
            {
                $this->WriteAMF3AssociativeArray($value);
            }
        }
        else if ($value instanceof AS3Object)
        {
            $this->WriteByte(AMF3TypeCode::Object->value);
            $this->WriteAMF3Object($value);
        }
        else if ($value instanceof DOMDocument)
        {
            $this->WriteByte(AMF3TypeCode::Xml2->value);
            $this->WriteAMF3XmlDocument($value);
        }
        else if ($value instanceof DateTime)
        {
            $this->WriteByte(AMF3TypeCode::DateTime->value);
            $this->WriteAMF3DateTime($value);
        }
        else if ($value instanceof ByteArray)
        {
            $this->WriteByte(AMF3TypeCode::ByteArray->value);
            $this->WriteByteArray($value);
        }
        else if ($value instanceof IDictionary)
        {
            $this->WriteByte(AMF3TypeCode::Dictionary->value);
            $this->WriteAMF3Dictionary($value);
        }
        else if (is_object($value))
        {
            $AS3Object = new AS3Object();
            $AS3Object->FromObject($value);
            $this->WriteAMF3($AS3Object);
        }
        else
        {
            throw new UnexpectedAMF("Unsupported serialization type");
        }
    }

    public function WriterAMF3String(string $value)
    {
        if ($value === '')
        {
            $this->WriterUInt29(1);
            return;
        }

        $index = array_search($value, $this->AMF3StringReferences, true);

        if ($index !== false)
        {
            // 写字符串引用
            $this->WriterUInt29($index << 1);
        }
        else
        {
            // 写新字符串
            $this->AMF3StringReferences[] = $value;
            $this->WriterUInt29(strlen($value) << 1 | 1);
            $this->WriteUTF($value);
        }
    }

    public function WriteAMF3AssociativeArray(array $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);

        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;
            $this->WriterUInt29(1);
            foreach($value as $key => $var)
            {
                $this->WriterAMF3String($key);
                $this->WriteAMF3($var);
            }

            $this->WriterAMF3String("");
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }

    public function WriteAMF3Array(array $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);
        
        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;
            $this->WriterUInt29(count($value) << 1 | 0x01);
            
            $this->WriterAMF3String("");
            
            foreach($value as $key => $var)
            {
                $this->WriteAMF3($var);
            }
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }


    public function WriteByteArray(ByteArray $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);
        
        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;
            $bytes = $value->ToArray();
            $this->WriterUInt29(strlen($bytes) << 1 | 0x01);
            $this->WriteRaw($bytes);
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }

    public function WriteAMF3Object(AS3Object $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);
        
        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;
            
            $Traitindex = array_search($value,$this->AMF3TraitsReferences,true);
            
            if($Traitindex === false)
            {
                $this->AMF3TraitsReferences[] = $value->Trait;

                $this->WriterUInt29(
                ($value->Trait->Externalizable ? 0x00 : $value->Trait->MemberCount()) << 4 |
                ($value->Trait->Dynamic ? 0x01 : 0x00) << 3 |
                ($value->Trait->Externalizable ? 0x01 : 0x00) << 2 |
                0x01 << 1 | 
                0x01
                );
                $this->WriterAMF3String($value->Trait->ClassName);

                if(!$value->Trait->Externalizable)
                {
                    foreach($value->Trait->Members as $Member => $var)
                    {
                        $this->WriterAMF3String($var);
                    }
                }
            }
            else
            {
                $this->WriterUInt29($index << 2 | 0x00 << 1 | 0x01);
            }

            if($value->Trait->Externalizable)
            {
                $Externizable = $value->ToObject();
                /**
                 * @var IExternalizable $Externizable
                 */
                if($Externizable instanceof IExternalizable)
                {
                    $Externizable->WriteExternal(new DataOutput($this));
                }
                else
                {
                    throw new UnexpectedAMF("Cannot find externalized ".$value->Trait->ClassName." class");
                }
            }
            else
            {
                for ($i=0; $i < $value->Trait->MemberCount(); $i++) 
                { 
                    $this->WriteAMF3($value->Values[$i]);
                }

                if($value->Trait->Dynamic)
                {
                    foreach($value->DynamicMembersAndValues as $key => $var)
                    {
                        $this->WriterAMF3String($key);
                        $this->WriteAMF3($var);
                    }

                    $this->WriterAMF3String("");
                }
            }
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }
    /**
     * 对XMLDocument的序列化支持
     */
    public function WriteAMF3XmlDocument(DOMDocument $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);

        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;

            $text = $value->saveXml();

            $this->WriterUInt29(strlen($text) << 1 | 0x01);
            $this->WriteUTF($text);
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }

    public function WriteAMF3DateTime(DateTime $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);
        
        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;
            $this->WriterUInt29(1);

            $epoch = new DateTime('1970-01-01', new DateTimeZone('UTC'));

            $utcTime = (clone $value)->setTimezone(new DateTimeZone('UTC'));

            $diffSec = $utcTime->getTimestamp() - $epoch->getTimestamp();

            $ms = $diffSec * 1000 + (int)($utcTime->format('u') / 1000);

            $this->WriteDouble((float)$ms);
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    }

    public function WriteAMF3Dictionary(IDictionary $value)
    {
        $index = array_search($value,$this->AMF3ObjectReferences,true);
        
        if($index === false)
        {
            $this->AMF3ObjectReferences[] = $value;

            $this->WriterUInt29( count($value) << 1 | 0x01);
            $this->WriterUInt29(1); // 标志位，无用忽略即可

            /** @var KeyValuePairs $key */
            foreach($value->ToArray() as $key)
            {
                $this->WriteAMF3($key->Key);
                $this->WriteAMF3($key->Key);
            }
        }
        else
        {
            $this->WriterUInt29($index << 1 | 0x00);
        }
    
    }

    // public function WriteAMF3ObjectVector(ObjectVector $value):void
    // {
    //     $index = array_search($value,$this->AMF3ObjectReferences,true);
        
    //     if($index === false)
    //     {
    //         $this->AMF3ObjectReferences[] = $value;

    //         $this->WriterUInt29( count($value) << 1 | 0x01);
    //         $this->WriterUInt29(1); // 标志位，无用忽略即可
    //         $this->WriterAMF3String($value->getTypeIdentifier());

    //         foreach($value as $key => $var)
    //         {
    //             $this->WriteAMF3($var);
    //         }
    //     }
    //     else
    //     {
    //         $this->WriterUInt29($index << 1 | 0x00);
    //     }
    // }

}