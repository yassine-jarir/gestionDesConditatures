<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject{
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
         'role'
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
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
            'skills' => 'array'
        ];
    }

    public function cvs(){
        return $this->hasOne(Cv::class);
    }

    public function jobs_apps(){
        return $this->hasMany(Job::class);
    }
    
    public function competences(){
        return $this->belongsToMany(Competence::class);
    }

    // roels 
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRecruteur(): bool
    {
        return $this->role === 'recruteur';
    }

    public function isCandidat(): bool
    {
        return $this->role === 'candidat';
    }
}
