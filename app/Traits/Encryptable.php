<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    /**
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->shouldEncrypt($key) && $this->isEncrypted($value)) {
            $value = Crypt::decrypt($this->stripPrefix($value));
        }

        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function shouldEncrypt($key)
    {
        if (!isset($this->encrypts)) {
            return false;
        }
        return in_array($key, $this->encrypts);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isEncrypted($value)
    {
        return strpos((string)$value, $this->getElocryptPrefix()) === 0;
    }

    /**
     * @return string
     */
    protected function getElocryptPrefix()
    {
        return Config::has('elocrypt.prefix') ? Config::get('elocrypt.prefix') : '__SECURED__:';
    }

    protected function stripPrefix($value)
    {
        return str_replace($this->getElocryptPrefix(), '', $value);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->shouldEncrypt($key) && !$this->isEncrypted($value)) {
            $value = $this->getElocryptPrefix() . Crypt::encrypt($value);
        }
        return parent::setAttribute($key, $value);
    }
}
