<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActivityMessage
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $activity_message
 * @property int $sender_id
 * @property int $activity_id
 * @property-read \App\Models\Activity $activity
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereActivityMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $sender
 */
class ActivityMessage extends Model
{
    use HasFactory;

    public function activity()
    {
        return $this->belongsTo('App\Models\Activity');
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User');
    }
}
