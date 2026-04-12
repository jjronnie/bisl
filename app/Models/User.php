<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\SplitsUserName;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SplitsUserName;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'member_id',
        'google_id',
        'must_change_password',
        'created_by',
        'status'
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }




    public function member()
    {
        return $this->hasOne(Member::class);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'creator');
    }

    public function transfersMade()
    {
        return $this->hasMany(Transfer::class, 'transferred_by');
    }

    /**
     * Automatically split name into first_name and last_name
     * If setting multiple words, first word is first_name, second word is last_name
     * If single word, it's first_name and last_name is empty
     */
    public static function splitName(string $name): array
    {
        $parts = array_filter(explode(' ', trim($name)));

        if (count($parts) >= 2) {
            return [
                'first_name' => $parts[0],
                'last_name' => $parts[1],
            ];
        }

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => '',
        ];
    }
}
