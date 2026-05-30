<?php
namespace Aurora\Flash\AMF3;


/**
 * IExternalizable 接口在将类编码到数据流中时提供了对序列化的控制。
 * 
 * The IExternalizable interface provides control over serialization of a class as it is encoded into a data stream.
 */
interface IExternalizable
{
    /**
     * 一个类实现此方法，通过调用 IDataInput 接口的方法从数据流中解码自身。
     * 
     * A class implements this method to decode itself from a data stream by calling the methods of the IDataInput interface. 
     */
    public function ReadExternal(IDataInput $input):void;
    /**
     * 一个类实现此方法，通过调用 IDataOutput 接口的方法将自身编码到数据流中。
     * 
     * A class implements this method to encode itself for a data stream by calling the methods of the IDataOutput interface.
     */
    public function WriteExternal(IDataOutput $output):void;

}