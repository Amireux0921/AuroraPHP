<?php
/*
 * @Description: 此文件是 AuroraAMF 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 22:21:28
 * @LastEditTime: 2026-05-29 22:23:38
 */
namespace Aurora\Collections;


interface IList extends ICollection , IEnumerable
{
    public function Add(mixed $value):int;

    public function Contains(mixed $value):bool;

    public function Clear():void;

    public function IndexOf(mixed $value);

    public function Insert(int $index,mixed $value):void;

    public function Remove(mixed $value):void;

    public function RemoveAt(int $index):void;

    public function IsReadOnly():bool;

    public function IsFixedSize():bool;
}