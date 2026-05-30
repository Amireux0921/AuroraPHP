<?php
/**
 * Apache Flex 4.16.1 API 参考
 * URI:https://flex.apache.org/asdoc/mx/messaging/messages/ErrorMessage.html
 */
namespace Aurora\Flash\Flex\Messaging\Messages;

use Aurora\Flash\Attribute\VoClassTag;
use Exception;

/**
 * ErrorMessage 类用于报告消息系统中的错误。 错误消息只会在收到以下消息时出现 系统
 */
#[VoClassTag('')]
class ErrorMessage extends AcknowledgeMessage
{
    public string $faultCode = '';

    public string $faultString = '';

    public string $faultDetail = '';

    public mixed $rootCause = null;

    public static function GetErrorMessage(Exception $exception):self
    {
        $message = new self();
        $message->rootCause = str_replace("\\",".",get_class($exception));
        $message->faultCode = $exception->getCode();
        $message->faultString = $exception->getMessage();
        $message->faultDetail = $exception->getTraceAsString();
        return $message;
    }
}