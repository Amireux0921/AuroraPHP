<?php
/*
 * @Description: 此文件是 Aurora 的一部分
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-29 22:29:44
 * @LastEditTime: 2026-05-29 22:29:50
 */
namespace Aurora\Collections;

interface IEnumerable
{
    public function GetEnumerable():IEnumerable;
}