<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Division;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'gender',
        'role',
        'division_id',
    ];

    // --- INI BAGIAN PENTING YANG KURANG TADI ---
    public function division()
    {
        // Memberitahu Laravel bahwa User ini milik satu Division
        return $this->belongsTo(Division::class, 'division_id');
    }

    // Helper untuk cek apakah dia admin
    public function isAdmin() {
        return $this->role === 'admin';
    }

    // Asesor untuk mendapatkan panggilan (Pak/Bu) otomatis
    public function getSapaanAttribute()
    {
        return $this->gender == 'L' ? 'Pak' : 'Bu';
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
        ];
    }
}