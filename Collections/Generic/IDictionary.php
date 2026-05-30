<?php
/*
 * @Description: 通用字典接口，支持全类型键值
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-30
 * @LastEditTime: 2026-05-30
 */
namespace Aurora\Collections\Generic;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Aurora\Collections\KeyValuePairs;

interface IDictionary extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * 设置键值对
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set(mixed $key, mixed $value): void;

    /**
     * 根据键获取值，支持默认值
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get(mixed $key, mixed $default = null): mixed;

    /**
     * 判断指定键是否存在
     * @param mixed $key
     * @return bool
     */
    public function containsKey(mixed $key): bool;

    /**
     * 移除指定键
     * @param mixed $key
     * @return void
     */
    public function remove(mixed $key): void;

    /**
     * 清空所有数据
     * @return void
     */
    public function clear(): void;

    /**
     * 获取所有原始键集合
     * @return mixed[]
     */
    public function getKeys(): array;

    /**
     * 获取所有值集合
     * @return mixed[]
     */
    public function getValues(): array;

    /**
     * 转为 KeyValuePairs 数组
     * @return KeyValuePairs[]
     */
    public function ToArray(): array;
}