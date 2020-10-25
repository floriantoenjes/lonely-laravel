<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChatMessage
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $chat_message
 * @property int $sender_id
 * @property int $receiver_id
 * @property-read \App\Models\User $receiver
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChatMessage extends Model
{
    use HasFactory;

    public function sender() {
        return $this->belongsTo('App\Models\User');
    }

    public function receiver() {
        return $this->belongsTo('App\Models\User');
    }
}
