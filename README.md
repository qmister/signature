

## 异常捕捉错误

~~~
UnexpectedValueException sign/timestamp/appId未定义
Qmister\Exception\SignatureInvalidException appId和sign不正确
Qmister\Exception\ExpiredException 签名失效
~~~

## 实例化

- $appId 随机32位

  > 可以根据用户ID进行加密生成 
  >
  > https://packagist.org/packages/hashids/hashids
  >
  > composer require hashids/hashids
  >
  > $appId = (new Hashids('',32))->encode($userId)

- $appSecret 随机32位

- $leeway 当前请求timestamp的生命周期内有效

~~~
$sign = new \Qmister\OpenApiSign($appId,$appSecret,$leeway=60);
~~~

## 客户端使用

~~~
// 客户端待发送的数据包
$data = array(
    'sex'       => '1',
    'age'       => '16',
    'addr'      => 'Qmister',
    'appid'     => $appid,//必传
    'timestamp' => time(),//必传
);
function sign($appSecret, array $input = [])
{
    // 对数组的值按key排序
    ksort($input);
    // 生成url的形式
    $params = http_build_query($input);
    // 生成sign
    $sign = md5($params . $appSecret);
    return $sign;
}
$data['sign'] = sign($appSecret, $data);
~~~

## 服务端进行验证

~~~
//根据请求过来的$data数据解析出appid，然后在根据appid查到appSecret
$sign->verifySign($appSecret, $data);
~~~

> Java md5 加密 org.apache.commons.codec.digest.DigestUtils.md5Hex($params . $appSecret)
