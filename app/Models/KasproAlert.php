<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasproAlert extends Model
{
    protected $table = 'kaspro_alerts';
    
    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'title',
        'message',
        'data',
        'source_type',
        'source_id',
        'is_read',
        'is_resolved',
        'resolved_at'
    ];
    
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
    
    public function getSeverityBadgeAttribute()
    {
        $badges = [
            'info' => 'bg-blue-100 text-blue-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'critical' => 'bg-orange-100 text-orange-800',
            'emergency' => 'bg-red-100 text-red-800',
        ];
        
        $class = $badges[$this->severity] ?? 'bg-gray-100 text-gray-800';
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $class . '">' . ucfirst($this->severity) . '</span>';
    }
}