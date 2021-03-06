<?php

namespace App\Models\Auth\User\Traits\Relations;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\SocialAccount;
use App\Models\NetLicensing\NlShopToken;
use App\Models\NetLicensing\NlValidation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait UserRelations
{
    /**
     * Relation with role
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    /**
     * Relation with social provider
     *
     * @return HasMany
     */
    public function providers()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function nlValidation()
    {
        return $this->hasOne(NlValidation::class);
    }

    public function nlShopToken()
    {
        return $this->hasOne(NlShopToken::class);
    }
}
