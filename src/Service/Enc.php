<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @author Al Zee <z@alz.ee>
 * @version
 * @todo
 */

namespace App\Service;

class Enc
{
    private $key;
    private $cipher = "aes-128-gcm";

    public function __construct()
    {
        $this->key = base64_decode($_ENV['AES_KEY']);
    }

    public function enc(string $plaintext)
    {
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        // Concise description about "options" parameter!
        // https://www.php.net/manual/en/function.openssl-encrypt.php#126267
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $this->key, 1, $iv, $tag);
        return $this->base64url_encode($ciphertext) . '.' . base64_encode($iv) . '.' . base64_encode($tag);
    }

    public function dec(string $ciphertext_iv)
    {
        $t = explode('.', $ciphertext_iv);
        $ciphertext = $this->base64url_decode($t[0]);
        $iv = base64_decode($t[1]);
        $tag = base64_decode($t[2]);
        $original_plaintext = openssl_decrypt($ciphertext, $this->cipher, $this->key, 1, $iv, $tag);
        return $original_plaintext;
    }

    // https://www.php.net/manual/en/function.base64-encode.php#123098
    public function base64url_encode($string) {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($string));
    }

    public function base64url_decode($string) {
        return base64_decode(str_replace(['-','_'], ['+','/'], $string));
    }

}
