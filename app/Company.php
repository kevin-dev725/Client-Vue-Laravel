<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * App\Company
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Client[] $clients
 * @property-read Collection|User[] $users
 * @property-read int|null $clients_count
 * @property-read int|null $users_count
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @mixin Eloquent
 */
class Company extends Model
{

    use SerializesData;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
