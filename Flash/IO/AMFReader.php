<?php
namespace Aurora\Flash\IO;

use Aurora\Flash\AMF0TypeCode;
use Aurora\Flash\AMF3\ByteArray;
use Aurora\Flash\AMF3\ClassDefinition;
use Aurora\Flash\AMF3\DataInput;
use Aurora\Flash\AMF3\IExternalizable;
use Aurora\Flash\AMF3TypeCode;
use Aurora\Flash\AS3Object;
use Aurora\Flash\ASObject;
use Aurora\Flash\Exceptions\UnexpectedAMF;
use Aurora\IO\BinaryReader;
use DateTime;
use DOMDocument;
class AMFReader extends BinaryReader
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

    public function ReadAMF0():mixed
    {
        $type = $this->ReadByte();
        $Tag = AMF0TypeCode::tryFrom($type);

        return match ($Tag)
        {
            AMF0TypeCode::Number            => $this->ReadDouble(),
            AMF0TypeCode::Boolean           => $this->ReadBoolean(),
            AMF0TypeCode::String            => $this->ReadString(),
            AMF0TypeCode::Null              => null,
            AMF0TypeCode::Undefined         => null,
            AMF0TypeCode::LongString        => $this->ReadLongString(),
            AMF0TypeCode::ASObject          => $this->ReadASObject(),
            AMF0TypeCode::Xml               => $this->ReadXMLDocument(),
            AMF0TypeCode::DateTime          => $this->ReadDateTime(),
            AMF0TypeCode::Array             => $this->ReadArray(),
            AMF0TypeCode::AssociativeArray  => $this->ReadAssociativeArray(),
            AMF0TypeCode::Reference         => $this->ReadReference(),
            AMF0TypeCode::CustomClass       =>$this->ReadTypeASObject(),
            AMF0TypeCode::AMF3Tag           => $this->ReadAMF3(),
            
                                        

            default => throw new UnexpectedAMF("0x".dechex($type)." is an unknown AMF0Tag marker"),
        };
    }

    public function ReadString(): string
    {
        $length = $this->ReadUShort();
        //echo $length."";
        return $this->ReadUTF($length);
    }

    public function ReadLongString(): string
    {
        $length = $this->ReadUInt();
        return $this->ReadUTF($length);
    }

    public function ReadReference():mixed
    {
        $index = $this->ReadUInt();
        if($index <0) throw new UnexpectedAMF("Illegal pointer ".dechex($index));
        return $this->AMF0ObjectReferences[$index] ?? throw new UnexpectedAMF("Cannot find data for reference pool pointer ".$index);

    }

    public function AddReference(mixed $value):void
    {
        $this->AMF0ObjectReferences[] = $value;
    }

    public function ReadArray(): array
    {
        $value = [];

        $length = $this->ReadUInt();

        for($i = 0; $i < $length; $i++)
        {
            $value[] = $this->ReadAMF0();
        }
        $this->AddReference($value);
        return $value;
    }

    public function ReadAssociativeArray(): array
    {
        $value = [];

        $length = $this->ReadUInt();


        for($i = 0; $i < $length; $i++)
        {
            $key = $this->ReadString();
            $var = $this->ReadAMF0();
            $value[$key] = $var;
        }
        $this->ReadEndMarkup();
        $this->AddReference($value);
        return $value;
    }

    public function ReadEndMarkup():void
    {
        $this->ReadUShort();
        $this->ReadByte();
    }

    public function ReadTypeASObject():ASObject
    {
        $object = new ASObject();

        $object->ClassName = $this->ReadString();


        while(true)
        {
            $key = $this->ReadString();
            if(empty($key)) break;

            $object->$key = $this->ReadAMF0();
        }
        $this->ReadEndMarkup();
        $this->AddReference($object);
        return $object;
    }

    public function ReadASObject(): ASObject
    {
        $object = new ASObject();

        while(true)
        {
            $key = $this->ReadString();
        
            if(empty($key)) break;

            $object->$key = $this->ReadAMF0();
        }
        $this->ReadEndMarkup();
        $this->AddReference($object);
        return $object;
    }

    public function ReadDateTime(): DateTime
    {
        $ms = $this->ReadDouble();
        $tzOffset = $this->ReadUShort();

        $sec = intval($ms / 1000);
        $dt = new DateTime("@{$sec}");
        return $dt;
    }

    public function ReadXMLDocument(): DOMDocument
    {
        $text = $this->ReadLongString();

        $xml = new DOMDocument();

        if($xml->loadXML($text) === false) throw new UnexpectedAMF("DOMDocument parsing failed");

        return $xml;
    }

    public function ReadAMF3():mixed
    {

        $type = $this->ReadByte();
        $Tag = AMF3TypeCode::tryFrom($type);
        return match($Tag)
        {
            AMF3TypeCode::Undefined             => null,
            AMF3TypeCode::Null                  => null,
            AMF3TypeCode::BooleanTrue           => true,
            AMF3TypeCode::BooleanFalse          => false,
            AMF3TypeCode::Integer               => $this->ReadUInt29(),
            AMF3TypeCode::Number                => $this->ReadDouble(),
            AMF3TypeCode::String                => $this->ReadAMF3String(),
            AMF3TypeCode::Xml                   => $this->ReadAMF3XmlDocument(),
            AMF3TypeCode::Xml2                  => $this->ReadAMF3XmlDocument(),
            AMF3TypeCode::DateTime              => $this->ReadAMF3DateTime(),
            AMF3TypeCode::Array                 => $this->ReadAMF3Array(),
            AMF3TypeCode::Object                => $this->ReadAS3Object(),
            AMF3TypeCode::ByteArray             =>$this->ReadAMF3ByteArray(),
            AMF3TypeCode::IntVector             => $this->ReadAMF3IntVector(),
            AMF3TypeCode::UIntVector            => $this->ReadAMF3UIntVector(),
            AMF3TypeCode::NumberVector          => $this->ReadAMF3DoubleVector(),
            AMF3TypeCode::ObjectVector          => $this->ReadAMF3ObjectVector(),
            AMF3TypeCode::Dictionary            => throw new UnexpectedAMF("Dictionary types are not currently supported for deserialization!"),
            default => throw new UnexpectedAMF("0x".dechex($type)." is an unknown AMF3Tag marker"),
        };
    }

    public function ReadAMF3ByteArray() : ByteArray
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length = $reference >> 1;

            $bytes = $this->ReadBytes($length);
            
            $value = new ByteArray($bytes);

            $this->AMF3ObjectReferences[] = $value;

            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }
    }

    /**
     * @return int[]
     */
    public function ReadAMF3IntVector():array
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length = $reference >> 1;

            $fixedVector = $this->ReadBoolean();
            
            $value = [];


            for($i = 0; $i < $length; $i++)
            {
                $value[] = $this->ReadInt();
            }

            $this->AMF3ObjectReferences[] = $value;

            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }

    }
    /**
     * @return uint[]
     */
    public function ReadAMF3UIntVector():array
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length = $reference >> 1;

            $fixedVector = $this->ReadBoolean();
            
            $value = [];


            for($i = 0; $i < $length; $i++)
            {
                $value[] = $this->ReadUInt();
            }

            $this->AMF3ObjectReferences[] = $value;

            
            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }
    }
    /**
     * @return double[]
     */
    public function ReadAMF3DoubleVector():array
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length = $reference >> 1;

            $fixedVector = $this->ReadBoolean();
            
            $value = [];


            for($i = 0; $i < $length; $i++)
            {
                $value[] = $this->ReadDouble();
            }

            $this->AMF3ObjectReferences[] = $value;

            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }

    }

    /** 
     * @return mixed[]
     */
    public function ReadAMF3ObjectVector():array
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length = $reference >> 1;

            $fixedVector = $this->ReadBoolean();

            $objectTypeName  = $this->ReadAMF3String();
            
            $value = [];

            for($i = 0; $i < $length; $i++)
            {
                $value[] = $this->ReadAMF3();
            }

            $this->AMF3ObjectReferences[] = $value;

            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }
    }
    /**
     * 不受支持 php没字典
     * @return void
     */
    // public function ReadAMF3Dictionary()
    // {
    //     $reference = $this->ReadUInt29();

    //     if(($reference & 0x01) === 0x01)
    //     {
    //         $length = $reference >> 1;

    //         $fixedVector = $this->ReadBoolean();
            
    //         $value = [];

    //         for($i = 0; $i < $length; $i++)
    //         {
    //             $key = $this->ReadAMF3();
    //             $value = $this->ReadAMF3();
    //         }

    //         $this->AMF3ObjectReferences[] = $value;

    //         return $value;
    //     }
    //     else
    //     {
    //         return $this->AMF3ObjectReferences[$reference >> 1];
    //     }
    // }


    public function ReadAS3Object() : AS3Object
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $reference = $reference >> 1;

            $trait = new ClassDefinition();

            if(($reference & 0x01) === 0x01)
            {
                $reference = $reference >> 1;

                $trait->Externalizable = ($reference & 0x01 ) === 0x01;

                $reference = $reference >> 1;

                $trait->Dynamic = ($reference & 0x01 ) === 0x01;

                $length  = $reference >> 1;

                $trait->ClassName = $this->ReadAMF3String();

                for($i = 0; $i < $length; $i++)
                {
                    $trait->Members[] = $this->ReadAMF3String();
                }

                $this->AMF3TraitsReferences[] = $trait;
            }
            else
            {
                $trait = $this->AMF3TraitsReferences[$reference >> 1];
            }

            $value = new AS3Object();
        
            $value->Trait = $trait;
            
            if($trait->Externalizable)
            {
                $externalizable = $value->ToObject();

                if($externalizable instanceof IExternalizable)
                {
                    $dataInput = new DataInput($this);
                    $externalizable->ReadExternal($dataInput);
                }
                else
                {
                    throw new UnexpectedAMF("Cannot find externalized ".$value->Trait->ClassName." class");
                }
            }
            else
            {
                for($i = 0; $i < $trait->MemberCount(); $i++)
                {
                    $value->Values[] = $this->ReadAMF3();
                }

                if($trait->Dynamic)
                {
                    while(true)
                    {
                        $key = $this->ReadAMF3String();

                        if(empty($key)) break;

                        $data = $this->ReadAMF3();

                        $value->DynamicMembersAndValues[$key] = $data;
                    }
                }
            }
            $this->AMF3ObjectReferences[] = $value;
            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }
    }

    public function ReadAMF3Array():array
    {
        $reference = $this->ReadUInt29();

        if(($reference & 0x01) === 0x01)
        {
            $length  = $reference >> 1;
            $value = [];

            while(true)
            {
                $key = $this->ReadAMF3String();

                if(empty($key)) break;

                $value[$key] = $this->ReadAMF3();
            }

            //$this->ReadByte(); // 标志位

            for ($i=0; $i < $length; $i++) 
            {
                $value[] = $this->ReadAMF3();
            }
            
            $this->AMF3ObjectReferences[] = $value;

            return $value;
        }
        else
        {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }

    }

    public function ReadAMF3XmlDocument(): DOMDocument
    {
        $reference = $this->ReadUInt29();

        //echo $reference;

        // 0x01 = empty XML (Adobe 规范)
        if ($reference === 0x01) {
            $doc = new DOMDocument('1.0', 'UTF-8');
            $doc->loadXML('<root/>'); // 或返回空文档
            return $doc;
        }

        // 新 XML
        if (($reference & 0x01) === 0x01) {
            $length = $reference >> 1;
            $text = $this->ReadUTF($length);

            $doc = new DOMDocument('1.0', 'UTF-8');
            if ($doc->loadXML($text) === false) {
                throw new UnexpectedAMF("DOMDocument parsing failed");
            }

            $this->AMF3ObjectReferences[] = $doc;
            return $doc;
        }

        // 引用 XML
        $index = $reference >> 1;

        if (!array_search($index, $this->AMF3ObjectReferences)) {
            throw new UnexpectedAMF(
                "AMF3 XML reference {$index} does not exist (raw {$reference})"
            );
        }

        return $this->AMF3ObjectReferences[$index];
    }

    public function ReadAMF3DateTime(): DateTime
    {
        $reference = $this->ReadUInt29();

        if (($reference & 0x01) === 0x01) {
            $milliseconds = $this->ReadDouble();

            $timestamp = (int)($milliseconds / 1000);
            $dt = new DateTime("@{$timestamp}");

            $this->AMF3ObjectReferences[] = $dt;
            return $dt;
        } else {
            return $this->AMF3ObjectReferences[$reference >> 1];
        }
    }

    public function ReadAMF3String(): string
    {
        $reference = $this->ReadUInt29();

        // empty string optimization
        if ($reference === 0x01) {
            return '';
        }

        if (($reference & 0x01) === 0x01) {
            $length = $reference >> 1;
            $value = $this->ReadUTF($length);

            if ($value !== '') {
                $this->AMF3StringReferences[] = $value;
            }
            return $value;
        }

        $index = $reference >> 1;

        if (!array_key_exists($index, $this->AMF3StringReferences)) {
            throw new UnexpectedAMF("String reference out of range: {$index}");
        }

        return $this->AMF3StringReferences[$index];
    }

    public function ReadUInt29(): int
    {
        $b = $this->ReadByte();

        // 1-byte
        if (($b & 0x80) === 0) {
            return $b;
        }

        $value = ($b & 0x7F) << 7;
        $b = $this->ReadByte();

        // 2-byte
        if (($b & 0x80) === 0) {
            return $value | $b;
        }

        $value = ($value | ($b & 0x7F)) << 7;
        $b = $this->ReadByte();

        // 3-byte
        if (($b & 0x80) === 0) {
            return $value | $b;
        }

        // 4-byte（⚠️ 第四字节不左移）
        $value = ($value | ($b & 0x7F)) << 8;
        $b = $this->ReadByte();

        return $value | $b;
    }
}