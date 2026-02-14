<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int|null $company_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $username
 * @property string $password
 * @property int $user_type 1:super_admin,2:admin,3:agent
 * @property string|null $agent_code
 * @property string|null $remember_token
 * @property string|null $password_reset_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAgentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePasswordResetToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasActivityLog, HasDeletedBy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_code',
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'username',
        'password',
        'password_reset_token',
        'user_type_id',
        'profile_photo',
        'profile_path',
        'added_by',
        'updated_by',
        'deleted_by'
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
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user_type()
    {
        return $this->belongsTo(UserType::class);
    }

    public function user()
    {
        return $this->belongsTo([User::class, 'added_by', 'id'], [User::class, 'updated_by', 'id'], [User::class, 'deleted_by', 'id']);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,     // Related model
            'user_permissions',    // Pivot table
            'user_id',             // Foreign key on pivot table for User
            'permission_id'        // Foreign key on pivot table for Permission
        )->withTimestamps();
    }

    public function hasPermission($perm)
    {
        if (is_array($perm)) {
            // Check if user has any permission in the array
            return $this->permissions->pluck('permission_name')
                ->intersect($perm)
                ->isNotEmpty();
        }
        return $this->permissions->contains('permission_name', $perm);
    }

    public function hasPermissionInRange($minId, $maxId)
    {
        // Filter user's permissions based on the dynamic range
        $validPermissions = $this->permissions
            ->filter(fn($p) => $p->id >= $minId && $p->id <= $maxId);

        // Returns true if user has **any permission** in the range
        return $validPermissions->isNotEmpty();
    }


    // checking if there is any permission for the user in the company 
    public function hasAnyPermission(array $permissionNames, $companyId = null): bool
    {
        $query = \DB::table('user_permissions')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('user_permissions.user_id', $this->id)
            ->whereIn('permissions.permission_name', $permissionNames);

        // Only filter by company if a companyId is provided
        if (!is_null($companyId)) {
            $query->where(function ($q) use ($companyId) {
                $q->where('user_permissions.company_id', $companyId)
                    ->orWhereNull('user_permissions.company_id'); // include global permissions
            });
        }

        return $query->exists();
    }
}
