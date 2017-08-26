<?php
function clean ($text) {
	$text = trim($text);
	$text = str_replace("&nbsp;", "", $text);
	return $text;
}

function random_string($length = 5) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function getcurrentpath()
{   $curPageURL = "";
    if ($_SERVER["HTTPS"] != "on")
            $curPageURL .= "http://";
     else
        $curPageURL .= "https://" ;
    if ($_SERVER["SERVER_PORT"] == "80")
        $curPageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     else
        $curPageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        $count = strlen(basename($curPageURL));
        $path = substr($curPageURL,0, -$count);
    return $path ;
}

/**
 * Implements AES-256 encryption/decryption in CBC mode.
 *
 * PBKDF2 is used for creation of encryption key.
 * HMAC is used to authenticate the encrypted message.
 *
 * Requires PHP 5.3 and higher
 *
 * Gist: https://gist.github.com/eugef/3d44b2e0a8a891432c65
 */
class McryptCipher
{
    const PBKDF2_HASH_ALGORITHM = 'SHA256';
    const PBKDF2_ITERATIONS = 64000;
    const PBKDF2_SALT_BYTE_SIZE = 32;
    // 32 is the maximum supported key size for the MCRYPT_RIJNDAEL_128
    const PBKDF2_HASH_BYTE_SIZE = 32;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $secureEncryptionKey;

    /**
     * @var string
     */
    private $secureHMACKey;

    /**
     * @var string
     */
    private $pbkdf2Salt;

    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Compares two strings.
     *
     * This method implements a constant-time algorithm to compare strings.
     * Regardless of the used implementation, it will leak length information.
     *
     * @param string $knownHash The string of known length to compare against
     * @param string $userHash   The string that the user can control
     *
     * @return bool true if the two strings are the same, false otherwise
     *
     * @see https://github.com/symfony/security-core/blob/master/Util/StringUtils.php
     */
    private function equalHashes($knownHash, $userHash)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($knownHash, $userHash);
        }

        $knownLen = strlen($knownHash);
        $userLen = strlen($userHash);

        if ($userLen !== $knownLen) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $knownLen; $i++) {
            $result |= (ord($knownHash[$i]) ^ ord($userHash[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
    }

    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     *
     * @param string $algorithm The hash algorithm to use. Recommended: SHA256
     * @param string $password The password
     * @param string $salt A salt that is unique to the password
     * @param int $count Iteration count. Higher is better, but slower. Recommended: At least 1000
     * @param int $key_length The length of the derived key in bytes
     * @param bool $raw_output If true, the key is returned in raw binary format. Hex encoded otherwise
     * @return string A $key_length-byte key derived from the password and salt
     *
     * @see https://defuse.ca/php-pbkdf2.htm
     */
    private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true)) {
            trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
        }
        if ($count <= 0 || $key_length <= 0) {
            trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
        }

        if (function_exists('hash_pbkdf2')) {
            // The output length is in NIBBLES (4-bits) if $raw_output is false!
            if (!$raw_output) {
                $key_length *= 2;
            }
            return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = strlen(hash($algorithm, '', true));
        $block_count = ceil($key_length / $hash_length);

        $output = '';
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack('N', $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output) {
            return substr($output, 0, $key_length);
        } else {
            return bin2hex(substr($output, 0, $key_length));
        }
    }

    /**
     * Creates secure PBKDF2 derivatives out of the password.
     *
     * @param null $pbkdf2Salt
     */
    private function derivateSecureKeys($pbkdf2Salt = null)
    {
        if ($pbkdf2Salt) {
            $this->pbkdf2Salt = $pbkdf2Salt;
        }
        else {
            $this->pbkdf2Salt = mcrypt_create_iv(self::PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM);
        }

        list($this->secureEncryptionKey, $this->secureHMACKey) = str_split(
            $this->pbkdf2(self::PBKDF2_HASH_ALGORITHM, $this->password, $this->pbkdf2Salt, self::PBKDF2_ITERATIONS, self::PBKDF2_HASH_BYTE_SIZE * 2, true),
            self::PBKDF2_HASH_BYTE_SIZE
        );
    }

    /**
     * Calculates HMAC for the message.
     *
     * @param string $message
     * @return string
     */
    private function hmac($message)
    {
        return hash_hmac(self::PBKDF2_HASH_ALGORITHM, $message, $this->secureHMACKey, true);
    }

    /**
     * Encrypts the input text
     *
     * @param string $input
     * @return string Format: hmac:pbkdf2Salt:iv:encryptedText
     */
    public function encrypt($input)
    {
        $this->derivateSecureKeys();

        $mcryptIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        // By default mcrypt_create_iv() function uses /dev/random as a source of random values.
        // If server has low entropy this source could be very slow.
        // That is why here /dev/urandom is used.
        $iv = mcrypt_create_iv($mcryptIvSize, MCRYPT_DEV_URANDOM);

        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secureEncryptionKey, $input, MCRYPT_MODE_CBC, $iv);

        $hmac = $this->hmac($this->pbkdf2Salt . $iv . $encrypted);

        return implode(':', array(
            base64_encode($hmac),
            base64_encode($this->pbkdf2Salt),
            base64_encode($iv),
            base64_encode($encrypted)
        ));
    }

    /**
     * Decrypts the input text.
     *
     * @param string $input Format: hmac:pbkdf2Salt:iv:encryptedText
     * @return string
     */
    public function decrypt($input)
    {
        list($hmac, $pbkdf2Salt, $iv, $encrypted) = explode(':', $input);

        $hmac = base64_decode($hmac);
        $pbkdf2Salt = base64_decode($pbkdf2Salt);
        $iv = base64_decode($iv);
        $encrypted = base64_decode($encrypted);

        $this->derivateSecureKeys($pbkdf2Salt);

        $calculatedHmac = $this->hmac($pbkdf2Salt . $iv . $encrypted);

        if (!$this->equalHashes($calculatedHmac, $hmac)) {
            trigger_error('HMAC ERROR: Invalid HMAC.', E_USER_ERROR);
        }

        // mcrypt_decrypt() pads the *RETURN STRING* with nulls ('\0') to fill out to n * blocksize.
        // rtrim() is used to delete them.
        return rtrim(
            mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secureEncryptionKey, $encrypted, MCRYPT_MODE_CBC, $iv),
            "\0"
        );
    }

}
?>