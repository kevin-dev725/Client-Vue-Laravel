<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\ClientImport
 *
 * @property int $id
 * @property int $user_id
 * @property string $csv
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $status
 * @property array|null $errors
 * @property array|null $exception
 * @property array|null $invalid_row
 * @property-read Collection|Activity[] $activity
 * @property-read User $user
 * @property-read int|null $activity_count
 * @method static Builder|ClientImport newModelQuery()
 * @method static Builder|ClientImport newQuery()
 * @method static Builder|ClientImport query()
 * @mixin Eloquent
 */
class ClientImport extends Model
{
    use LogsActivity;

    const STATUS_PENDING = 'pending', STATUS_STARTED = 'started', STATUS_ERROR = 'error', STATUS_FINISHED = 'finished';
    /**
     * @var string
     */
    protected static $logName = 'client_import';
    /**
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * @var array
     */
    protected static $ignoreChangedAttributes = ['updated_at'];
    /**
     * @var array
     */
    protected static $logAttributes = ['user_id', 'created_at', 'status', 'errors', 'invalid_row'];
    protected $guarded = [];
    protected $casts = [
        'errors' => 'array',
        'invalid_row' => 'array',
        'exception' => 'array',
    ];
    protected $hidden = [
        'csv',
        'exception',
    ];

    /**
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($user = $this->user) {
            switch ($eventName) {
                case 'created':
                    return "{$user->email} queued a csv import at {$this->created_at->toDateTimeString()} ";
                case 'updated':
                    return "Queued csv import by {$user->email} has {$this->status}.";
                case 'deleted':
                    return "Client import by {$user->email} has been deleted";
            }
        }
        return "Client import ID: {$this->id} has been {$eventName}.";
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
