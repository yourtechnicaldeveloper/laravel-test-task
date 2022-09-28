<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable {

    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'photo',
        'password',
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

    protected function password(): Attribute
    {
        return new Attribute(
            function ($value)
            {
                return ($value);
            },
            function ($value)
            {
                return bcrypt($value);
            }
        );

        // return Attribute::make(
        //     get: fn ($value) => ucfirst($value),
        //     set: fn ($value) => strtolower($value),
        // );
    }

    public function setPhotoAttribute($value) {
        $this->attributes['photo'] = (!is_string($value)) ? upload_files(request(), "photo", "photo", DIRECTORY_USER_PHOTOS, ((isset($this->attributes['photo'])) ? $this->attributes['photo'] : NULL)) : $value;
    }

    public function getPhotoAttribute($value) {
        return uploaded_file_url($value, DIRECTORY_USER_PHOTOS);
    }

}
