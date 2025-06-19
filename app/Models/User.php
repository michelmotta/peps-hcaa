<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'expertise',
        'username',
        'file_id',
        'password',
        'biography',
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
     * Get the array of data that should be searchable.
     *
     * @return array<string, mixed> Array of searchable user fields.
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->title,
            'email' => $this->email,
            'username' => $this->username,
            'expertise' => $this->expertise,
        ];
    }

    /**
     * Set the user's password.
     *
     * Automatically hashes the password if needed.
     *
     * @param string|null $password The plain password to set.
     * @return void
     */
    public function setPasswordAttribute($password): void
    {
        if (is_null($password)) {
            return;
        }

        if (Hash::needsRehash($password)) {
            $password = Hash::make($password);
        }

        $this->attributes['password'] = $password;
    }

    /**
     * Get the information entries associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Information>
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * Get the information entries associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Information>
     */
    public function information()
    {
        return $this->belongsToMany(Information::class)->withTimestamps();
    }

    /**
     * Get the profiles associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Profile>
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class)->withTimestamps();
    }

    /**
     * Get the lessons created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Lesson>
     */
    public function createdLessons()
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the lessons the user is subscribed in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Lesson>
     */
    public function subscribedLessons()
    {
        return $this->belongsToMany(Lesson::class)
            ->using(LessonUser::class)
            ->withPivot(['score', 'finished', 'finished_at'])
            ->withTimestamps();
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\History::class);
    }

    /**
     * Check if the user has a specific profile by name.
     *
     * @param string $profile The name of the profile to check.
     * @return bool True if the user has the profile, false otherwise.
     */
    public function hasProfile(string $profile): bool
    {
        if ($this->relationLoaded('profiles')) {
            return $this->profiles->contains('name', $profile);
        }

        return null !== $this->profiles()->where('name', $profile)->first();
    }

    /**
     * Check if the user has any profile from a list of names.
     *
     * @param array<int, string> $profiles Array of profile names to check.
     * @return bool True if the user has at least one of the profiles, false otherwise.
     */
    public function hasAnyProfile(array $profiles): bool
    {
        if ($this->relationLoaded('profiles')) {
            return $this->profiles->whereIn('name', $profiles)->isNotEmpty();
        }

        return $this->profiles()->whereIn('name', $profiles)->exists();
    }

    /**
     * Check if the user has only the 'Professor' profile.
     *
     * @return bool True if the user has only one profile and it is 'Professor'.
     */
    public function hasOnlyProfessorProfile()
    {
        return $this->profiles->count() === 1 && $this->profiles->first()->name === 'Professor';
    }
}
