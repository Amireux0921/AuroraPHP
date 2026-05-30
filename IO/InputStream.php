<?php
namespace Aurora\IO;

class InputStream extends Stream
{
    public function __construct()
    {
        $this->Buffer = file_get_contents('php://input');
        $this->offset = 0;
    }

    /**
     * 追加写入数据（只追加缓冲区，不移动读写指针）
     * @param string $data
     * @return void
     */
    public function Write(string $data): void
    {
        $this->Buffer .= $data;
    }

    /**
     * 从当前指针位置读取指定字节，自动偏移指针
     * @param int $length
     * @return string
     */
    public function Read(int $length): string
    {
        $totalLen = strlen($this->Buffer);
        if ($this->offset >= $totalLen) {
            return '';
        }
        $readLen = min($length, $totalLen - $this->offset);
        $result = substr($this->Buffer, $this->offset, $readLen);
        $this->offset += $readLen;
        return $result;
    }

    /**
     * 设置指针偏移位置
     * @param int $offset
     * @return bool
     */
    public function Seek(int $offset): bool
    {
        $totalLen = strlen($this->Buffer);
        if ($offset < 0 || $offset > $totalLen) {
            return false;
        }
        $this->offset = $offset;
        return true;
    }


    /**
     * 清空缓冲区，指针归零
     * @return void
     */
    public function Flush(): void
    {
        $this->Close();
    }

    /**
     * 关闭流，重置状态
     * @return void
     */
    public function Close(): void
    {
        $this->Buffer = '';
        $this->offset = 0;
    }

    /**
     * 析构自动关闭流
     */
    public function __destruct()
    {
        $this->Close();
    }
}