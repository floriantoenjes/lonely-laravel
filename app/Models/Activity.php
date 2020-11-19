<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $description
 * @property string $latitude
 * @property string $longitude
 * @property string $city
 * @property int $postcode
 * @property string $address
 * @property string $date
 * @property int $creator_id
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityMessage[] $activityMessages
 * @property-read int|null $activity_messages_count
 * @property-read \App\Models\User $creator
 */
class Activity extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany('App\Models\User')->withTimestamps();
    }

    public function activityMessages()
    {
        return $this->hasMany('App\Models\ActivityMessage');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User');
    }
}
