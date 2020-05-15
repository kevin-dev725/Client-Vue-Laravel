<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Webpatser\Countries\Countries;

/**
 * App\Country
 *
 * @property int $id
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string $country_code
 * @property string|null $currency
 * @property string|null $currency_code
 * @property string|null $currency_sub_unit
 * @property string|null $currency_symbol
 * @property int|null $currency_decimals
 * @property string|null $full_name
 * @property string $iso_3166_2
 * @property string $iso_3166_3
 * @property string $name
 * @property string $region_code
 * @property string $sub_region_code
 * @property int $eea
 * @property string|null $calling_code
 * @property string|null $flag
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country query()
 * @mixin Eloquent
 */
class Country extends Countries
{
    /**
     * @param $code
     * @return Country
     */
    public static function getByCode($code)
    {
        return static::query()->where('iso_3166_2', $code)->first();
    }

    /**
     * @return Country
     */
    public static function getDefaultCountry()
    {
        return static::query()->where('iso_3166_2', config('settings.default_country'))->first();
    }

    protected function getCountries()
    {
        //Get the countries from the JSON file
        if (!$this->countries || sizeof($this->countries) == 0) {
            $this->countries = json_decode(file_get_contents(__DIR__ . '/../storage/app/countries.json'), true);
        }

        //Return the countries
        return $this->countries;
    }
}
