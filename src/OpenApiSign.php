<?php


namespace Qmister;

use UnexpectedValueException;
use Qmister\Exception\ExpiredException;
use Qmister\Exception\SignatureInvalidException;


class OpenApiSign
{
    /**
     * @var int
     */
    protected $leeway;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * @var string
     */
    protected $appId;

    /**
     * Signature constructor.
     * @param $appId
     * @param $appSecret
     * @param int $leeway
     */
    public function __construct($appId, $appSecret, $leeway = 60)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->leeway    = $leeway;
    }

    /**
     * 生成签名
     * @param array $input
     * @return string
     */
    public function buildSign(array $input)
    {
        // 对数组的值按key排序
        ksort($input);
        // 生成url的形式
        $params = http_build_query($input);
        // 生成sign
        $sign = md5($params . $this->appSecret);
        return $sign;
    }

    /**
     * 验证签名
     * @param array $input
     * @return bool
     * @throws ExpiredException
     * @throws SignatureInvalidException
     */
    public function verifySign(array $input)
    {
        if (empty($input['sign'])) {
            throw new UnexpectedValueException('sign Undefined');
        }
        if (empty($input['appId']) && $input['appId'] != $this->appId) {
            throw new UnexpectedValueException('appId Signature failed');
        }
        if (empty($input['timestamp'])) {
            throw new UnexpectedValueException('timestamp Undefined');
        }
        if (time() - $input['timestamp'] > $this->leeway) {
            throw new ExpiredException('timestamp Expired');
        }
        if ($input['appId'] != $this->appId) {
            throw new SignatureInvalidException('appId Signature failed');
        }
        $sign = (string)$input['sign'];
        unset($input['sign']);
        ksort($input);
        $params = http_build_query($input);
        $sign2  = md5($params . $this->appSecret);
        if ($sign != $sign2) {
            throw new SignatureInvalidException('Signature verification failed');
        }
        return true;
    }
}