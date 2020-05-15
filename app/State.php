<?php

namespace App;

use AdrianMejias\States\States;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\State
 *
 * @property int $id
 * @property string $iso_3166_2
 * @property string $name
 * @property string $country_code
 * @property-read Collection|County[] $counties
 * @property-read int|null $counties_count
 * @method static Builder|State newModelQuery()
 * @method static Builder|State newQuery()
 * @method static Builder|State query()
 * @mixin Eloquent
 */
class State extends States
{
    /**
     * @return HasMany
     */
    public function counties()
    {
        return $this->hasMany(County::class);
    }
}
