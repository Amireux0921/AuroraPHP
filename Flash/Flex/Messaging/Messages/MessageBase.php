<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 23:00:56
 * @LastEditTime: 2026-05-29 23:28:04
 */
namespace Aurora\Flash\Flex\Messaging\Messages;

class MessageBase extends AbstractMessage
{
    public  mixed $clientId = null;

    public  string $destination = '';

    public  string $messageId = '';

    public  int $timestamp = 0;
    
    public  int $timeToLive = 0;

    public  mixed $body = null;

    public  array $headers = [];  

    public function GetHeader(string $name): mixed
    {
        return $this->headers[$name] ?? null;
    }

    public function SetHeader(string $name, mixed $value): void
    {
        $this->headers[$name] = $value;
    }

    public function HeaderExists(string $name): bool
    {
        return in_array($name, $this->headers);
    }

    public function Copy(): self
    {
        return new self();
    }

    protected function CopyImpl(self $message): self
    {
        if($message == null)
        {
            $message = new MessageBase();
        }
        $message->clientId = $this->clientId;
        $message->destination = $this->destination;
        $message->messageId = $this->messageId;
        $message->timestamp = $this->timestamp;
        $message->timeToLive = $this->timeToLive;
        $message->body = $this->body;
        $message->headers = $this->headers;
        return $message;
    }

    public function GetFlexClientId(): string
    {
        if($this->HeaderExists('DSId'))
        {
            return $this->GetHeader('DSId');
        }
        return '';
    }

    public function SetFlexClientId(string $value): void
    {
        $this->SetHeader('DSId', $value);
    }

    public function GetIndent(int $indentLevel): string
    {
        return '';
    }
}