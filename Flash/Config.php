<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-30 13:39:55
 * @LastEditTime: 2026-05-30 20:09:25
 */
namespace Aurora\Flash;

class Config
{
    /**
     * Debug调试模式
     */
    public const DEBUG = true;

    /**
     * Header 头部设置
     * @var array
     */
    public static array $Header = [
        'enable' => true,
        'execute' =>[
            ['Name'=>"debug","MustUnderstand"=>true,'Handler'=>\Aurora\Flash\Flex\Messaging\Messages\MessageBase::class],
        ],
    ];
    /**
     * 外部化对象映射
     * @var array<string,string> $Mapping 
     * 
     * key 为 映射类名 如 "flex.messaging.message.ErrorMessage"
     * value 实体类完整命名空间  通过xxx::class获取
     */
    public static array $Mapping = [
        'Flex.Messaging.Messages.Test' => \app\services\api\message::class
    ];
}