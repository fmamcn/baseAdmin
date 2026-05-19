<?php
namespace app\common\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JwtAuth
{
    /**
     * 生成 Token
     * @param int $uid 用户ID
     * @param string $type 类型：access 或 refresh
     * @return string
     */
    public static function createToken($uid, $type = 'access')
    {
        $time = time(); // 当前时间戳
        $secret = config('api.key');  // 私钥Key

        // 设置过期时间：access 2小时，refresh 7天
        $exp = (int)config('api.exp');
        $refresh_exp = (int)config('api.refresh_exp') * 86400;
        $expireTime = $type === 'access' ? $exp : $refresh_exp;

        // 域名
        $domain = request()->domain();

        $payload = [
            'iss' => $domain,             // 签发者
            'iat' => $time,               // 签发时间 (必须)
            'nbf' => $time,               // 生效时间 (必须)
            'exp' => $time + $expireTime, // 过期时间 (必须)
            'uid'  => (int)$uid,          // 用户ID
            'type' => $type,              // 区分 token 类型
            'jti'  => bin2hex(random_bytes(16)) // 唯一标识，防止重复
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    /**
     * 校验并解析 Token
     * @param string $token 前端传来的纯 Token 字符串（不含 Bearer）
     * @param string $requiredType 期望的 token 类型 (access 或 refresh)
     * @return array|object 返回解析后的载荷对象，或包含错误信息的数组
     */
    public static function verifyToken($token, $requiredType = 'access')
    {
        $secret = config('api.key');  // 私钥Key

        if (empty($token)) {
            return ['status' => 'error', 'msg' => 'Token不能为空'];
        }

        try {
            // firebase/php-jwt 6.x 必须使用 Key 对象传参
            $key = new Key($secret, 'HS256');
            $decoded = JWT::decode($token, $key);

            // 1. 校验 Token 类型是否匹配（防止用 refresh_token 访问业务接口）
            if (!isset($decoded->type) || $decoded->type !== $requiredType) {
                return ['status' => 'error', 'msg' => 'Token类型错误'];
            }

            // 2. 双重保险：手动校验过期时间（兼容 PHP 7.1 环境）
            if (isset($decoded->exp) && $decoded->exp < time()) {
                throw new ExpiredException('Token has expired');
            }

            // 校验通过，返回解析后的对象
            return $decoded;

        } catch (ExpiredException $e) {
            // 明确返回“已过期”状态，方便前端捕获 401 后触发刷新逻辑
            return ['status' => 'expired', 'msg' => 'Token已过期'];
            
        } catch (SignatureInvalidException $e) {
            return ['status' => 'error', 'msg' => 'Token签名无效'];
            
        } catch (BeforeValidException $e) {
            return ['status' => 'error', 'msg' => 'Token尚未生效'];
            
        } catch (\Exception $e) {
            // 捕获其他异常（如格式错误、解析失败等）
            return ['status' => 'error', 'msg' => 'Token无效或格式错误'];
        }
    }
}