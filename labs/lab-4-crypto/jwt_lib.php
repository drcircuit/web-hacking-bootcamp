<?php
class JWT {
    public static function encode($payload, $key, $alg = 'HS256') {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        $segments = [];
        $segments[] = self::base64url_encode(json_encode($header));
        $segments[] = self::base64url_encode(json_encode($payload));
        $signing_input = implode('.', $segments);
        if ($alg === 'none') {
            return $signing_input . '.';
        }
        $signature = hash_hmac('sha256', $signing_input, $key, true);
        $segments[] = self::base64url_encode($signature);
        return implode('.', $segments);
    }

    public static function decode($token, $key) {
        $parts = explode('.', $token);
        if (count($parts) < 2) return null;
        list($header64, $payload64, $sig64) = array_pad($parts, 3, '');
        $header = json_decode(self::base64url_decode($header64), true);
        $payload = json_decode(self::base64url_decode($payload64), true);

        if ($header['alg'] === 'none') {
            return $payload;
        }

        $valid_sig = self::base64url_encode(hash_hmac('sha256', "$header64.$payload64", $key, true));
        if ($sig64 === $valid_sig) {
            return $payload;
        }
        return null;
    }

    private static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>
