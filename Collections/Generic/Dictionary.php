<?php
/*
 * @Description: 全类型键值字典，Key & Value 均支持 mixed
 * @Author: Lonely
 * @Email: shangwucang@foxmail.com
 * @Date: 2026-05-30
 * @LastEditTime: 2026-05-30 08:53:09
 */
namespace Aurora\Collections\Generic;

use Traversable;
use ArrayIterator;
use Aurora\Collections\KeyValuePairs;

class Dictionary implements IDictionary
{
    /**
     * 底层存储：key = 序列化后的唯一字符串，value = [原始键, 原始值]
     * @var array<string, array{0: mixed, 1: mixed}>
     */
    private array $storage = [];

    public function __construct(array $initItems = [])
    {
        foreach ($initItems as $k => $v) {
            $this->set($k, $v);
        }
    }

    /**
     * 将任意 mixed 键转为唯一字符串标识
     */
    private function serializeKey(mixed $key): string
    {
        // 利用 serialize 保证任意类型生成唯一字符串
        return serialize($key);
    }

    // ===================== ArrayAccess 接口实现 =====================
    public function offsetExists(mixed $offset): bool
    {
        $hashKey = $this->serializeKey($offset);
        return array_key_exists($hashKey, $this->storage);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $hashKey = $this->serializeKey($offset);
        return $this->storage[$hashKey][1] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $hashKey = $this->serializeKey($offset);
        $this->storage[$hashKey] = [$offset, $value];
    }

    public function offsetUnset(mixed $offset): void
    {
        $hashKey = $this->serializeKey($offset);
        unset($this->storage[$hashKey]);
    }

    // ===================== Countable 接口实现 =====================
    public function count(): int
    {
        return count($this->storage);
    }

    // ===================== IteratorAggregate 接口实现 =====================
    public function getIterator(): Traversable
    {
        $data = [];
        foreach ($this->storage as $item) {
            [$rawKey, $rawVal] = $item;
            $data[$rawKey] = $rawVal;
        }
        return new ArrayIterator($data);
    }

    // ===================== IDictionary 自定义方法实现 =====================
    public function set(mixed $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : $default;
    }

    public function containsKey(mixed $key): bool
    {
        return $this->offsetExists($key);
    }

    public function remove(mixed $key): void
    {
        $this->offsetUnset($key);
    }

    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * 获取所有原始 Key
     * @return mixed[]
     */
    public function getKeys(): array
    {
        $keys = [];
        foreach ($this->storage as $item) {
            $keys[] = $item[0];
        }
        return $keys;
    }

    /**
     * 获取所有原始 Value
     * @return mixed[]
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->storage as $item) {
            $values[] = $item[1];
        }
        return $values;
    }

    /**
     * 转为 KeyValuePairs 数组
     * @return KeyValuePairs[]
     */
    public function ToArray(): array
    {
        $arr = [];
        foreach ($this->storage as $item) {
            [$rawKey, $rawVal] = $item;
            $arr[] = new KeyValuePairs($rawKey, $rawVal);
        }
        return $arr;
    }
}