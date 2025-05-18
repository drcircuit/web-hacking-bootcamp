<?php
class JWT {
    private $secret;

    public function __construct($secret) {
        $this->secret = $secret;
    }

    private function base64_url_encode($data) {
        return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
    }

    private function base64_url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function encode($payload) {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $header_encoded = $this->base64_url_encode(json_encode($header));
        $payload_encoded = $this->base64_url_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $this->secret, true);
        $signature_encoded = $this->base64_url_encode($signature);
        return "$header_encoded.$payload_encoded.$signature_encoded";
    }

    public function decode($token) {
        list($header_encoded, $payload_encoded, $signature_encoded) = explode('.', $token);
        $signature = $this->base64_url_decode($signature_encoded);
        $expected_signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $this->secret, true);
        if (hash_equals($expected_signature, $signature)) {
            $payload = json_decode($this->base64_url_decode($payload_encoded), true);
            return $payload;
        } else {
            return null;
        }
    }
}
?>