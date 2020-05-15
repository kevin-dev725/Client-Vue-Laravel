<?php

namespace App\Services;

class HumanNameParser
{

    public function splitFullName($full_name)
    {
        $full_name = trim($full_name);
        $last_name = (strpos($full_name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $full_name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $full_name));
        return [
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
    }
}
