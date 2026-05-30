<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 23:48:20
 * @LastEditTime: 2026-05-30 10:43:58
 */
namespace Aurora\Flash\IO;

use Aurora\IO\Stream;

class AMFSerializer extends AMFWriter
{
    public function __construct(Stream $stream)
    {
        parent::__construct($stream);
    }

    public function WriteMessage(AMFMessage $Message):void
    {
        $this->WriteShort($Message->Version->value);

        $this->WriteShort(count($Message->Headers));

        foreach($Message->Headers as $Header)
        {
            $this->WriteHeader($Header);
        }

        $this->WriteShort(count($Message->Bodys));

        foreach($Message->Bodys as $Body)
        {
            $this->WriteBody($Body);
        }
    }

    private function WriteHeader(AMFHeader $Header):void
    {
        $this->ResetReferences();

        $this->WriteString($Header->Name);
        $this->WriteBool($Header->MustUnderstand);
        $this->WriteInt(-1);
        $this->WriteAMF0($Header->Content);

    }

    private function WriteBody(AMFBody $Body):void
    {
        $this->ResetReferences();

        $this->WriteString($Body->Target);
        $this->WriteString($Body->Response);
        $this->WriteInt(-1);
        $this->WriteAMF0($Body->Content);
    }

}