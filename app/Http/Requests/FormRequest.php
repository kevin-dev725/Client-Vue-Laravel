<?php

namespace App\Http\Requests;

use App\Base64Image;
use App\Trainer;
use App\User;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{

    /**
     * @param null $guard
     * @return User
     */
    public function user($guard = null)
    {
        return parent::user($guard);
    }

    /**
     * @inheritdoc
     */
    public function route($param = null, $castTo = null)
    {
        $val = parent::route($param);
        if (is_object($val) || !$castTo) return $val;
        return $castTo::find($val);
    }

    /**
     * @param $key
     * @param null $default
     * @return Base64Image
     */
    public function getImage($key, $default = null)
    {
        if (!$this->has($key) || empty($image = $this->get($key))) {
            return $default;
        }
        return new Base64Image($image);
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($key, $default);
        }
        return $default;
    }

    /**
     * Check if request is from the webapp.
     * @return bool
     */
    public function isWebappRequest()
    {
        return $this->getBoolean('_webapp');
    }

    public function getBoolean($key, $default = false)
    {
        if ($this->has($key)) {
            return getBoolean($this->get($key));
        }
        return $default;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $name = $this->get('first_name') . ' ' . $this->get('last_name');
        if ($this->filled('middle_name') && $this->get('middle_name') != 'undefined') {
            $name = $this->get('first_name') . ' ' . $this->get('middle_name') . ' ' . $this->get('last_name');
        }
        
        return $name;
    }
}
