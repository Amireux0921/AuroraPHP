<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 22:51:54
 * @LastEditTime: 2026-05-29 22:59:21
 */
namespace Aurora\Flash\Flex\Messaging\Messages;

abstract class AbstractMessage
{
    public abstract mixed $clientId;

    public abstract string $destination;

    public abstract string $messageId;

    public abstract int $timestamp;
    
    public abstract int $timeToLive;

    public abstract mixed $body;

    public abstract array $headers; 

    public abstract function GetHeader(string $name):mixed;

    public abstract function SetHeader(string $name, mixed $value):void;

    public abstract function HeaderExists(string $name):bool;

    public abstract function GetFlexClientId():string;

    public abstract function Copy():self;
}