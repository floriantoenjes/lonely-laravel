<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserLonelySetting
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $latitude
 * @property string $longitude
 * @property string $city
 * @property string|null $lonely_since
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereLonelySince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $radius
 * @property int $meet_up_age_from
 * @property int $meet_up_age_to
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereMeetUpAgeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereMeetUpAgeTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereRadius($value)
 * @property int $postcode
 * @property string $address
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLonelySetting wherePostcode($value)
 */
class UserLonelySetting extends Model
{
    use HasFactory;
}
