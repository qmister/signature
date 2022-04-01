<?php

namespace Qmister\Tests;

use PHPUnit\Framework\TestCase;
use Qmister\Exception\ExpiredException;
use Qmister\Exception\SignatureInvalidException;
use Qmister\OpenApiSign;

class OpenApiSignTest extends TestCase
{
    /**
     * @throws ExpiredException
     * @throws SignatureInvalidException
     */
    public function testEq()
    {
        $appid     = 'djskaldjkasj';
        $appSecret = 'dasjdkalsjdkasjdkla';
        $data      = array(
            'sex'       => '1',
            'age'       => '16',
            'addr'      => 'Qmister',
            'appId'     => $appid,//必传
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
        $sign         = new OpenApiSign($appid, $appSecret);
        $foo          = $sign->verifySign($data);
        $this->assertEquals(true, $foo);
    }

    /**
     * @throws ExpiredException
     * @throws SignatureInvalidException
     */
    public function testSign()
    {
        $appid        = 'djskaldjkasj';
        $appSecret    = 'dasjdkalsjdkasjdkla';
        $data         = array(
            'sex'       => '1',
            'age'       => '16',
            'addr'      => 'Qmister',
            'appId'     => $appid,//必传
            'timestamp' => time(),//必传
        );
        $sign         = new OpenApiSign($appid, $appSecret);
        $data['sign'] = $sign->buildSign($data);
        $this->assertIsBool(true, $sign->verifySign($data));
    }
}