<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\ForgotPasswordMail;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable, CanResetPasswordTrait;

    protected $appends = ['avatar_url'];

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
        'sector_id',
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
            'name' => $this->name,
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
    public function subscriptions()
    {
        return $this->belongsToMany(Lesson::class)
            ->using(LessonUser::class)
            ->withPivot(['id', 'score', 'finished', 'finished_at'])
            ->withTimestamps();
    }

    public function studentSubscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(LessonUser::class, Lesson::class, 'user_id', 'lesson_id');
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

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_id');
    }

    public function isTeacherOf(Lesson $lesson): bool
    {
        return $lesson->teacher->id === $this->id;
    }

    /**
     * Get only the student's completed subscriptions (lessons).
     */
    public function completedSubscriptions()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->wherePivot('finished', true);
    }

    /**
     * Get only the student's pending subscriptions (lessons).
     */
    public function pendingSubscriptions()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->wherePivot('finished', false);
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function lastLogin()
    {
        return $this->hasOne(UserLogin::class)->latestOfMany();
    }

    public function sendPasswordResetNotification($token)
    {
        $url = route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        Mail::to($this->getEmailForPasswordReset())->send(new ForgotPasswordMail($this, $url));
    }

    public function getAvatarUrl(int $size = 100): string
    {
        if ($this->file) {
            return asset('storage/' . $this->file->path);
        }

        $initial = strtoupper(mb_substr($this->name, 0, 1));
        return "https://placehold.co/{$size}x{$size}/EBF4FF/7F9CF5?text={$initial}";
    }
}
