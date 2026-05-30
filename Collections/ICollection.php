<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 22:28:35
 * @LastEditTime: 2026-05-29 22:31:00
 */
namespace Aurora\Collections;


interface ICollection extends IEnumerable
{
    public function CopyTo():void;

    public function Count():int;

    public function SyncRoot():mixed;

    public function IsSynchronized():bool;
}