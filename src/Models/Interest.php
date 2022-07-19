<?php

namespace NotificationChannels\ExpoPushNotifications\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $experienceId
 * @property string $key
 * @property string $value
 */
class Interest extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'experience_id',
        'key',
        'value',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table doesn't have an incrementing ID.
     */
    public $incrementing = false;

    /**
     * Interest constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->dispatchesEvents = config('exponent-push-notifications.interests.database.events');

        $this->table = config('exponent-push-notifications.interests.database.table_name');

        parent::__construct($attributes);
    }
}
