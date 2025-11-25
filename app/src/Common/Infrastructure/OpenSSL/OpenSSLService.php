<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\OpenSSL;

use GridCP\Common\Domain\Exceptions\OpenSSLDecryptError;
use GridCP\Common\Domain\Exceptions\OpenSSLEncryptError;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

readonly class OpenSSLService
{
    private  string $CIPHERING;
    private string $PII_KEY;


    public function __construct(private ContainerBagInterface $params){
        $this->CIPHERING = $this->params->get('GridCP.CIPHERING');
        $this->PII_KEY = $this->params->get('GridCP.PII_KEY');
    }


    public function encrypt(string $data):string
    {
        $ivLen = openssl_cipher_iv_length($this->CIPHERING);
        $iv = openssl_random_pseudo_bytes($ivLen);
        $ciphertext_raw = openssl_encrypt($data, $this->CIPHERING, $this->PII_KEY, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->PII_KEY, $as_binary=true);
        $result=  base64_encode( $iv.$hmac.$ciphertext_raw );
        return ($result === false)
            ?throw new OpenSSLEncryptError()
            : $result;
    }

    public function decrypt(string $data):string
    {
        $c = base64_decode($data);
        $ivLen = openssl_cipher_iv_length($this->CIPHERING);
        $iv = substr($c, 0, $ivLen);
        $hmac = substr($c, $ivLen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivLen+$sha2len);
        $result = openssl_decrypt($ciphertext_raw, $this->CIPHERING, $this->PII_KEY, $options=OPENSSL_RAW_DATA, $iv);
        return ($result === false)
            ?throw new OpenSSLDecryptError()
            : $result;
    }

}
