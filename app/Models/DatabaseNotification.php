<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class DatabaseNotification extends BaseDatabaseNotification
{
    protected $table = 'h2u_notifications';
    protected $primaryKey = 'hn_id';

    public $incrementing = false;
    protected $keyType = 'string';

    // Set the morphable column names
    protected $morphClass = 'App\\Models\\DatabaseNotification';
    
    // Override the morph column names
    public function getMorphClass()
    {
        return 'App\\Models\\DatabaseNotification';
    }

    protected $casts = [
        'hn_data' => 'array',
        'hn_read_at' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['hn_id'] ?? null;
    }

    public function setIdAttribute($value): void
    {
        $this->attributes['hn_id'] = $value;
    }

    public function getTypeAttribute()
    {
        return $this->attributes['hn_type'] ?? null;
    }

    public function setTypeAttribute($value): void
    {
        $this->attributes['hn_type'] = $value;
    }

    public function setDataAttribute($value): void
    {
        $this->attributes['hn_data'] = is_string($value) ? $value : json_encode($value);
    }

    public function setReadAtAttribute($value): void
    {
        $this->attributes['hn_read_at'] = $value;
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('hn_read_at');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('hn_read_at');
    }

    public function markAsRead(): void
    {
        if (is_null($this->hn_read_at)) {
            $this->forceFill(['hn_read_at' => $this->freshTimestamp()])->save();
        }
    }

    public function markAsUnread(): void
    {
        if (! is_null($this->hn_read_at)) {
            $this->forceFill(['hn_read_at' => null])->save();
        }
    }

    public function getDataAttribute()
    {
        $value = $this->attributes['hn_data'] ?? null;

        if (is_array($value) || is_null($value)) {
            return $value;
        }

        return json_decode($value, true);
    }

    public function getReadAtAttribute()
    {
        return $this->attributes['hn_read_at'] ?? null;
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo('hn_notifiable', 'hn_notifiable_type', 'hn_notifiable_id');
    }

    /**
     * Override to use custom column names
     */
    public function getNotifiableTypeAttribute()
    {
        return $this->attributes['hn_notifiable_type'] ?? null;
    }

    public function setNotifiableTypeAttribute($value)
    {
        $this->attributes['hn_notifiable_type'] = $value;
    }

    public function getNotifiableIdAttribute()
    {
        return $this->attributes['hn_notifiable_id'] ?? null;
    }

    public function setNotifiableIdAttribute($value)
    {
        $this->attributes['hn_notifiable_id'] = $value;
    }
}
