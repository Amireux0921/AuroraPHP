<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-30 21:50:03
 * @LastEditTime: 2026-05-30 21:56:15
 */
namespace Aurora\Flash\Remoting;

use Aurora\Exceptions\AuroraException;
use Aurora\Flash\IO\AMFDeserializer;
use Aurora\Flash\IO\AMFSerializer;
use Aurora\IO\InputStream;
use Aurora\IO\MemoryStream;

/**
 * Flash Remoting网关是连接客户端与服务端的核心组件，负责处理AMF协议的序列化与反序列化
 * 
 * The Flash Remoting gateway is the core component that connects the client and the server, responsible for handling the serialization and deserialization of the AMF protocol.
 */
class Gateway 
{
    /**
     * 执行 Flash 远程处理 服务
     * 
     * Execute Flash Remoting Service
     * @param string $ServicePrefix 服务类起始前缀路命名空间
     * @throws AuroraException
     * @return string
     */
    public function Service(string $ServicePrefix = '\\app\\services\\') : string
    {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');

        $allHeaders = getallheaders() ?: [];

        $ContentType = trim($allHeaders['Content-Type'] ?? '');

        if(!str_starts_with($ContentType,'application/x-amf') || $requestMethod !== "POST") 
            throw new AuroraException("Please use the Action Message Format protocol for transmission");

        $Deserializer = new AMFDeserializer(new InputStream());

        $Message = $Deserializer->ReadMessage();

        $MemoryStream = new MemoryStream();

        $Serializer = new AMFSerializer($MemoryStream);

        $Serializer->WriteMessage($Message);

        return $Serializer->ToArray();
    }
}