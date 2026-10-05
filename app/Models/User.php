<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['fullname', 'email', 'phone', 'address', 'gender', 'birthday', 'last_login', 'password', 'status', 'role', 'points', 'membership', 'notes', 'password_reset_token', 'password_reset_expires_at'])]
#[Hidden(['password', 'remember_token', 'password_reset_token', 'password_reset_expires_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'fullname',
        'email',
        'phone',
        'address',
        'gender',
        'birthday',
        'last_login',
        'password',
        'status',
        'role',
        'points',
        'membership',
        'notes',
        'remember_token',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    /**
     * Get user address with fallback to notes
     */
    public function getResolvedAddressAttribute(): string
    {
        if (!empty($this->address)) {
            return $this->address;
        }
        if (!empty($this->notes) && str_contains($this->notes, 'Địa chỉ:')) {
            $parts = explode('Địa chỉ:', $this->notes, 2);
            return trim($parts[1]);
        }
        return $this->notes ?? '';
    }

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
            'status' => 'boolean',
        ];
    }

    /**
     * Get the orders for this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the subscriptions for this user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the reviews made by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Add points and automatically update membership rank.
     */
    public function addPoints(float $amount): void
    {
        // Calculate points to add:
        // - If amount >= 1000 (detected as VND), add 1 point for every 1,000 VND
        // - If amount < 1000 (detected as USD), add 10 points for every 1 unit of USD
        $pointsToAdd = $amount >= 1000 ? (int) round($amount / 1000) : (int) round($amount * 10);
        if ($pointsToAdd <= 0) {
            return;
        }

        $this->points += $pointsToAdd;

        // Update membership rank based on new points
        // Thresholds:
        // - Bronze: < 100 points
        // - Silver: 100 to < 500 points
        // - Gold: 500 to < 2000 points
        // - Diamond: >= 2000 points
        if ($this->points >= 2000) {
            $this->membership = 'diamond';
        } elseif ($this->points >= 500) {
            $this->membership = 'gold';
        } elseif ($this->points >= 100) {
            $this->membership = 'silver';
        } else {
            $this->membership = 'bronze';
        }

        $this->save();
    }

    public function userCoupons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function savedCoupons(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withPivot('is_used', 'used_at')
            ->withTimestamps();
    }
}
