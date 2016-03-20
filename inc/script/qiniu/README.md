七牛云存储PHP SDK（非官方，更好用）
==============================

初始化
-----

```
$client = new QiniuClient($accessKey,$secretKey);
```

上传
----


###上传文件

```
$client->uploadFile('/local/files/file.txt','test_bucket_name','test/file.txt');
```
###上传内容作为文件存储

```
$client->upload('我是文件内容','test_bucket_name','test/file2.txt');
```
###存储远程文件

```
$client->uploadRemote('http://www.baidu.com/img/bdlogo.gif','test_bucket_name','test/file3.gif');
```

###上传凭证 - uploadToken （自制表单上传时需要）
```
$flag = array('scope'=>'test_bucket_name');
$client->uploadToken($flags);
```
$flags更多选项参加[文档](http://docs.qiniu.com/api/v6/put.html#uploadToken)



文件管理 - 单文件操作
------------------


###查看

```
$client->stat('test_bucket_name','test/file3.gif');
```
###复制

复制到原bucket

```
$client->copy('test_bucket_name','test/file3.gif','test/file4.gif');
```
复制到其它bucket

```
$client->copy('test_bucket_name','test/file3.gif','another_bucket_name','test/file4.gif');
```
###移动
在原bucket中移动

```
$client->move('test_bucket_name','test/file4.gif','test/file5.gif');
```
移动到其它bucket

```
$client->move('test_bucket_name','test/file3.gif','another_bucket_name','test/file5.gif');
```

###删除

```
$client->delete('test_bucket_name','test/file.txt');
```

文件管理 - 批量操作
----------------

```
$client->batch($operator,$files);
```
###批量查看文件
```
$client->batch('stat',array('bucket_name:test/test5.txt','bucket_name:test/test6.png'));
```
###批量复制文件
test/test5.txt复制到bucket_name:test/test6.txt

test/test6.txt复制到bucket_name:test/test7.txt

```
$client->batch('copy',array(
	array('bucket_name:test/test5.txt','bucket_name:test/test6.txt'),
	array('bucket_name:test/test7.txt','bucket_name:test/test8.txt')
));
```
###批量移动文件
同复制

```
$client->batch('move',array(
	array('bucket_name:test/test5.txt','bucket_name:test/test6.txt'),
	array('bucket_name:test/test7.txt','bucket_name:test/test8.txt')
));
```
###批量删除文件
```
$client->batch('delete',array('bucket_name:test/test5.txt','bucket_name:test/test6.png'));
```

##列出文件
详见[官方文档](http://docs.qiniu.com/api/v6/file-handle.html#list)

```
$client->listFiles($bucket,$limit=10,$prefix='test',$marker='');
```







