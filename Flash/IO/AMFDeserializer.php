<?php
namespace Aurora\Flash\IO;

use Aurora\Flash\Exceptions\UnexpectedAMF;
use Aurora\Flash\ObjectEncoding;
use Aurora\IO\Stream;

class AMFDeserializer extends AMFReader
{
    public function __construct(Stream $stream)
    {
        parent::__construct($stream);
    }
    public function ReadMessage():AMFMessage
    {
        $Encoding = ObjectEncoding::tryFrom($this->ReadUShort());
    
        if($Encoding !== null)
        {
            $Message = new AMFMessage();

            $HeaderCount = $this->ReadUShort();


            for ($i=0; $i < $HeaderCount ; $i++) 
            { 
                $Message->AddHeader($this->ReadHeader());
            }

            $BodyCount = $this->ReadUShort();


            for ($i=0; $i < $BodyCount ; $i++) 
            { 
                $Message->AddBody($this->ReadBody());
            }
            return $Message;
        }
        else
        {
            throw new UnexpectedAMF("Not an Action Message Format protocol");
        }
    }

    private function ReadBody():AMFBody
    {
        $this->ResetReferences();

        $Target = $this->ReadString();
        $Response = $this->ReadString();
        
        // echo $Target;
        // echo $Response;


        $this->ReadInt();

        $Content = $this->ReadAMF0();

        return new AMFBody($Target, $Response, $Content);
    }

    private function ReadHeader():AMFHeader
    {
        $this->ResetReferences();

        $Name = $this->ReadString();
        $MustUnderstand = $this->ReadBoolean();
        
        $this->ReadInt();

        $Content = $this->ReadAMF0();

        return new AMFHeader($Name, $MustUnderstand, $Content);
    }


}