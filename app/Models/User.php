<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [ 
             'name',
        'email',
        'phone',
        'password',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

  

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [
        'email'=>$this->email,
        'name'=>$this->name
      ];
    }
     public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin', 'admin');
    }

    public function isAdminUser(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin'], 'admin');
    }



    public function ownedWorkspaces()
{
    return $this->hasMany(\App\Models\Workspace::class, 'owner_id');
}

public function workspaces()
{
    return $this->belongsToMany(\App\Models\Workspace::class, 'workspace_user')
        ->withPivot('role')
        ->withTimestamps();
}








public function affiliateProfile()
{
    return $this->hasOne(\Modules\Affiliate\Models\AffiliateProfile::class);
}

public function affiliateReferrals()
{
    return $this->hasMany(\Modules\Affiliate\Models\AffiliateReferral::class, 'referred_user_id');
}

public function affiliateCommissions()
{
    return $this->hasMany(\Modules\Affiliate\Models\AffiliateCommission::class, 'referred_user_id');
}
  }
