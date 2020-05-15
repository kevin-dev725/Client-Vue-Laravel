<?php

use App\Services\Quickbooks;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Fluent;
use function GuzzleHttp\Psr7\mimetype_from_filename;

/**
 * @param $base64
 * @return string
 */
function base64_getMime($base64)
{
    $pos = strpos($base64, ';');
    return explode(':', substr($base64, 0, $pos))[1];
}

/**
 * @param $base64
 * @return string
 */
function base64_getExtension($base64)
{
    return explode('/', base64_getMime($base64))[1];
}

/**
 * @param $base64
 * @return string
 */
function base64_getContents($base64)
{
    return str_replace(' ', '+', str_replace('data:' . base64_getMime($base64) . ';base64,', '', $base64));
}

/**
 * @return int
 */
function getTimestampString()
{
    return (new DateTime())->getTimestamp();
}

/**
 * @return string
 */
function getUniqueString()
{
    return str_random() . md5(uniqid(str_random(), true));
}

/**
 * @param $mime
 * @return bool
 */
function isImageMime($mime)
{
    return in_array($mime, array_keys(config('image.extensions')));
}

/**
 * @param $mime
 * @return bool
 */
function isVideoMime($mime)
{
    return starts_with($mime, 'video/');
}

/**
 * @param $mime
 * @return string
 */
function getMimeExtension($mime)
{
    $extensions = array_merge(config('image.extensions'), [
        'video/mp4' => 'mp4',
        'video/webm' => 'webm'
    ]);
    return array_get($extensions, $mime);
}

/**
 * @param $filename
 * @return null|string
 */
function getMimeFromFilename($filename)
{
    return mimetype_from_filename($filename);
}

/**
 * @return FilesystemAdapter
 */
function disk_local()
{
    return \Illuminate\Support\Facades\Storage::disk('local');
}

/**
 * @return FilesystemAdapter
 */
function disk_public()
{
    return \Illuminate\Support\Facades\Storage::disk('public');
}

/**
 * @return FilesystemAdapter
 */
function disk_s3()
{
    return \Illuminate\Support\Facades\Storage::disk('s3');
}

/**
 * @param $file_path
 * @return string
 */
function full_local_path($file_path)
{
    return disk_local()->getDriver()->getAdapter()->getPathPrefix() . $file_path;
}

/**
 * @param \Symfony\Component\HttpFoundation\Request $request
 * @return null|string
 */
function getClientIp(\Symfony\Component\HttpFoundation\Request $request)
{
    return $request->headers->has('x-forwarded-for') ? $request->headers->get('x-forwarded-for') : $request->getClientIp();
}

/**
 * @param $key
 * @return bool
 */
function env_has($key)
{
    return env($key) !== null && !empty(trim(env($key)));
}

/**
 * @param $key
 * @param null $default
 * @return mixed|null
 */
function env_get($key, $default = null)
{
    return env_has($key) ? env($key) : $default;
}

/**
 * @return bool
 */
function isTestingEnvironment()
{
    return config('app.env') === 'testing';
}

/**
 * @param null $ext
 * @return string
 */
function uniqueFilename($ext = null)
{
    return str_replace('.', '', uniqid(str_random(18), true)) . ($ext ? '.' . $ext : '');
}

/**
 * @param $seconds
 * @return string
 */
function formatOffset($seconds)
{
    $negative = false;
    if ($seconds < 0) {
        $negative = true;
        $seconds = $seconds * -1;
    }
    $hours = floor($seconds / 3600);
    $mins = floor($seconds / 60 % 60);
    return sprintf(($negative ? '-' : '+') . '%02d:%02d', $hours, $mins);
}

/**
 * @param $timezone
 * @return string
 */
function timezone_offset($timezone)
{
    return formatOffset(Carbon::now()->setTimezone($timezone)->offset);
}

/**
 * @param float $amount
 * @return string
 */
function dollar($amount)
{
    return '$' . number_format($amount, 2);
}

/**
 * @param $path
 * @return string
 */
function getExtensionFromPath($path)
{
    return array_last(explode('.', $path));
}

function beginTransaction()
{
    \Illuminate\Support\Facades\DB::beginTransaction();
}

function commit()
{
    \Illuminate\Support\Facades\DB::commit();
}

function rollback()
{
    \Illuminate\Support\Facades\DB::rollback();
}

function transaction(Closure $closure)
{
    \Illuminate\Support\Facades\DB::transaction($closure);
}

/**
 * @param Model $model
 * @param array $attributes
 * @return array
 */
function getModelAttributes(Model $model, array $attributes)
{
    $arr = [];
    $model_attributes = $model->attributesToArray();
    foreach ($attributes as $attribute) {
        $arr[$attribute] = isset($model_attributes[$attribute]) ? $model_attributes[$attribute] : null;
    }
    return $arr;
}

/**
 * @param Blueprint $blueprint
 * @param string $column
 * @param string $references
 * @param string $foreignTable
 * @param string $onDelete
 * @return Fluent
 */
function foreign(Blueprint $blueprint, $column, $references, $foreignTable, $onDelete = 'restrict')
{
    $fluent = $blueprint->integer($column)->unsigned();
    $blueprint->foreign($column)->references($references)->on($foreignTable)->onDelete($onDelete);
    return $fluent;
}

/**
 * @param $value
 * @return bool
 */
function getBoolean($value)
{
    if ($value === 'true') return true;
    elseif ($value === 'false') return false;
    return boolval($value);
}

/**
 * @return Authenticatable|null
 */
function getAuthUser()
{
    return auth('web')->check() ? auth('web')->user() : auth('api')->user();
}

/**
 * @return bool
 */
function isLoggedIn()
{
    return auth('web')->check() || auth('api')->check();
}

/**
 * @param $seconds
 * @return string
 */
function formatSecondsToReadableTime($seconds)
{
    $hour = floor($seconds / 3600);
    $min = floor(($seconds - ($hour * 3600)) / 60);
    $sec = floor($seconds - (($hour * 3600) + ($min * 60)));

    $hourString = $hour ? $hour . ' ' . str_plural('hour', $hour) : null;
    $minString = $min ? $min . ' ' . str_plural('minute', $min) : null;
    $secString = $sec ? $sec . ' ' . str_plural('second', $sec) : null;

    $arrTime = [
        $hourString,
        $minString,
        $secString
    ];

    return trim(implode(" ", $arrTime));
}

/**
 * @param $by
 * @param $address
 * @return string
 */
function getAddress($by, $address)
{
    if (empty($address)) {
        return null;
    }
    $addressArr = explode(',', $address);
    switch ($by) {
        case 'street':
            return trim($addressArr[0]);
            break;
        case 'city':
            return trim($addressArr[1]);
            break;
        case 'state':
            $arr = explode(' ', trim($addressArr[2]));
            return trim($arr[0]);
            break;
        case 'postal_code':
            $arr = explode(' ', trim($addressArr[2]));
            return trim($arr[1]);
            break;
    }
}

function storage_prefix_dir($path)
{
    $dir = config('app.env');
    if (config('app.env') === 'local') {
        $dir = 'dev';
    }
    return implode('/', [$dir, ltrim($path, '/')]);
}

/**
 * @return Quickbooks
 */
function quickbooks()
{
    return app(Quickbooks::class);
}

/**
 * @param null $path
 * @param array $qs
 * @param null $secure
 * @return string
 */
function qs_url($path = null, $qs = array(), $secure = null)
{
    $url = app('url')->to($path, $secure);
    if (count($qs)) {

        foreach ($qs as $key => $value) {
            $qs[$key] = sprintf('%s=%s', $key, urlencode($value));
        }
        $url = sprintf('%s?%s', $url, implode('&', $qs));
    }
    return $url;
}

function phone_clean($phone_number)
{
    return str_replace(['+', ' ', '(', ')', '-'], '', $phone_number);
}

function str_strip_non_alphabets($str)
{
    return preg_replace("/[^A-Za-z]/", '', $str);
}

function in_production()
{
    return config('app.env') === 'prod' || config('app.env') === 'production';
}

/**
 * @return bool|string
 */
function temp_path()
{
    return tempnam(sys_get_temp_dir(), 'clientDomain');
}

if (!function_exists('escape_like')) {
    /**
     * @param $string
     * @return string
     */
    function escape_like($string)
    {
        $ret = str_replace(['%', '_'], ['\%', '\_'], DB::getPdo()->quote($string));
        return $ret && strlen($ret) >= 2 ? substr($ret, 1, strlen($ret) - 2) : $ret;
    }
}
