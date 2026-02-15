<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'user_id',
        'subdomain',
        'full_domain',
        'target_ip',
        'status',
        'radnet_response',
    ];

    protected function casts(): array
    {
        return [
            'radnet_response' => 'json',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the domain.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to limit 3 domains per user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if user has reached domain limit.
     */
    public static function canCreateNew($userId): bool
    {
        $user = User::find($userId);
        
        // If user has unlimited subdomains
        if ($user->hasUnlimitedSubdomains()) {
            return true;
        }

        // Check against user's limit
        $currentCount = self::where('user_id', $userId)->count();
        return $currentCount < $user->getSubdomainLimit();
    }

    /**
     * Get remaining subdomain slots for user.
     */
    public static function getRemainingSlots($userId): ?int
    {
        $user = User::find($userId);
        
        if ($user->hasUnlimitedSubdomains()) {
            return null; // unlimited
        }

        $currentCount = self::where('user_id', $userId)->count();
        return max(0, $user->getSubdomainLimit() - $currentCount);
    }
}

