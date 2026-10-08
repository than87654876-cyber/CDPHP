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

#[Fillable(['fullname', 'avatar', 'email', 'phone', 'address', 'company_address', 'other_address', 'default_address_type', 'gender', 'birthday', 'last_login', 'password', 'status', 'role', 'points', 'membership', 'notes', 'two_factor_enabled', 'password_reset_token', 'password_reset_expires_at'])]
#[Hidden(['password', 'remember_token', 'password_reset_token', 'password_reset_expires_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'fullname',
        'avatar',
        'email',
        'phone',
        'address',
        'company_address',
        'other_address',
        'default_address_type',
        'gender',
        'birthday',
        'last_login',
        'password',
        'status',
        'role',
        'points',
        'membership',
        'notes',
        'two_factor_enabled',
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
     * Get avatar URL or default fallback UI avatar
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            return \Illuminate\Support\Str::startsWith($this->avatar, 'http') ? $this->avatar : asset($this->avatar);
        }
        $displayName = $this->fullname ?: 'Khách hàng';
        return 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=ee4d2d&color=ffffff&bold=true';
    }

    /**
     * Get default delivery address text based on default_address_type
     */
    public function getDefaultAddressTextAttribute(): string
    {
        return match($this->default_address_type) {
            'company' => $this->company_address ?: ($this->address ?: $this->other_address ?: ''),
            'other' => $this->other_address ?: ($this->address ?: $this->company_address ?: ''),
            default => $this->address ?: ($this->company_address ?: $this->other_address ?: ''),
        };
    }

    /**
     * Get detailed membership rank status, color, benefits, and next tier progress
     */
    public function getMembershipDetailsAttribute(): array
    {
        $points = (int) ($this->points ?? 0);
        $tier = $this->membership ?: 'bronze';

        $tiers = [
            'bronze' => [
                'name' => 'Thành viên Đồng',
                'badge' => 'Đồng',
                'color' => '#b45309',
                'bg' => 'bg-amber-100 text-amber-800',
                'border' => 'border-amber-300',
                'icon' => 'fa-shield-halved',
                'min' => 0,
                'next_tier' => 'Bạc',
                'next_points' => 100,
                'benefits' => ['Tích 1 điểm cho mỗi 1.000đ', 'Nhận thông báo ưu đãi giờ ăn'],
            ],
            'silver' => [
                'name' => 'Thành viên Bạc',
                'badge' => 'Bạc',
                'color' => '#64748b',
                'bg' => 'bg-slate-200 text-slate-800',
                'border' => 'border-slate-300',
                'icon' => 'fa-medal',
                'min' => 100,
                'next_tier' => 'Vàng',
                'next_points' => 500,
                'benefits' => ['Tất cả quyền lợi hạng Đồng', 'Voucher giảm giá 10% hàng tháng', 'Ưu tiên chuẩn bị món'],
            ],
            'gold' => [
                'name' => 'Thành viên Vàng',
                'badge' => 'Vàng',
                'color' => '#eab308',
                'bg' => 'bg-yellow-100 text-yellow-900',
                'border' => 'border-yellow-400',
                'icon' => 'fa-crown',
                'min' => 500,
                'next_tier' => 'Kim Cương',
                'next_points' => 2000,
                'benefits' => ['Tất cả quyền lợi hạng Bạc', 'Freeship đơn từ 100.000đ', 'Quà tặng sinh nhật đặc biệt', 'Voucher độc quyền 20%'],
            ],
            'diamond' => [
                'name' => 'Thành viên Kim Cương',
                'badge' => 'Kim Cương',
                'color' => '#06b6d4',
                'bg' => 'bg-cyan-100 text-cyan-900',
                'border' => 'border-cyan-400',
                'icon' => 'fa-gem',
                'min' => 2000,
                'next_tier' => null,
                'next_points' => 2000,
                'benefits' => ['Tất cả quyền lợi hạng Vàng', 'Freeship mọi đơn hàng', 'Chăm sóc VIP 1:1', 'Voucher giảm tới 35%'],
            ],
        ];

        $current = $tiers[$tier] ?? $tiers['bronze'];
        $progress = 100;
        $neededPoints = 0;
        if ($current['next_tier']) {
            $range = $current['next_points'] - $current['min'];
            $currentProgress = max(0, $points - $current['min']);
            $progress = min(100, (int) round(($currentProgress / $range) * 100));
            $neededPoints = max(0, $current['next_points'] - $points);
        }

        return array_merge($current, [
            'progress' => $progress,
            'needed_points' => $neededPoints,
        ]);
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
