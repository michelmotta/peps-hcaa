<?php

namespace App\Models;

use App\Enums\CertificateTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    /** @use HasFactory<\Database\Factories\CertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'type',
        'uuid',
        'issued_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public static function registerCertificate(Lesson $lesson, User $user, CertificateTypeEnum $certificateType = CertificateTypeEnum::STUDENT): Certificate
    {
        $certificate = self::firstOrCreate(
            [
                'user_id'   => $user->id,
                'lesson_id' => $lesson->id,
                'type'      => $certificateType->value,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'issued_at' => now(),
            ]
        );

        $certificate->update([
            'issued_at' => now(),
        ]);

        return $certificate;
    }
}
