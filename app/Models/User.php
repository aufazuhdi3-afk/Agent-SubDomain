<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'subdomain_limit',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all domains associated with this user.
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get subdomain limit for this user.
     * Returns null if unlimited, or integer if limited.
     */
    public function getSubdomainLimit(): ?int
    {
        return $this->subdomain_limit;
    }

    /**
     * Check if user has unlimited subdomains.
     */
    public function hasUnlimitedSubdomains(): bool
    {
        return is_null($this->subdomain_limit);
    }

    /**
     * Get number of remaining subdomain slots for this user.
     * Returns null if unlimited, or integer if limited.
     */
    public function getRemainingSlots(): ?int
    {
        if ($this->hasUnlimitedSubdomains()) {
            return null; // Unlimited
        }

        $limit = $this->getSubdomainLimit();
        $used = $this->domains()->count();
        return max(0, $limit - $used);
    }
}
