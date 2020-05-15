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
 * App\QuickbooksImport
 *
 * @property int $id
 * @property int $user_id
 * @property string $path
 * @property string $status
 * @property array|null $errors
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activity
 * @property-read User $user
 * @method static Builder|QuickbooksImport newModelQuery()
 * @method static Builder|QuickbooksImport newQuery()
 * @method static Builder|QuickbooksImport query()
 * @mixin Eloquent
 * @property-read int|null $activity_count
 */
class QuickbooksImport extends Model
{
    use LogsActivity;

    const STATUS_PENDING = 'pending', STATUS_STARTED = 'started', STATUS_ERROR = 'error', STATUS_FINISHED = 'finished', STATUS_FINISHED_WITH_ERROR = 'finished_with_error';
    /**
     * @var string
     */
    protected static $logName = 'quickbooks_import';
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
    protected static $logAttributes = ['user_id', 'created_at', 'status', 'path'];
    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * @var array
     */
    protected $casts = [
        'errors' => 'json'
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
                    return "{$user->email} queued a quickbooks import at {$this->created_at->toDateTimeString()} ";
                case 'updated':
                    $status = str_replace('_', ' ', $this->status);
                    return "Quickbooks clients import by {$user->email} has {$status}.";
                case 'deleted':
                    return "Quickbooks import by {$user->email} has been deleted";
            }
        }
        return "Quickbooks import ID: {$this->id} has been {$eventName}.";
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
