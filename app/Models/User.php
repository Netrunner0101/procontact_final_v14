<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'nom',
        'prenom',
        'email',
        'telephone',
        'rue',
        'numero_rue',
        'ville',
        'code_postal',
        'pays',
        'password',
        'last_login_at',
        'password_reset_token',
        'password_reset_expires',
        'google_id',
        'apple_id',
        'provider',
        'avatar',
        'layout_preference',
        'role_id',
        'contact_id',
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
            'last_login_at' => 'datetime',
            'password_reset_expires' => 'datetime',
        ];
    }

    /**
     * Get the role of the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the contact record linked to this client user.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the contacts for the user.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the activities for the user.
     */
    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    /**
     * Get the appointments for the user.
     */
    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    /**
     * Get the admin user for this client.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the clients for this admin user.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(User::class, 'admin_user_id');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role->nom === Role::ADMIN;
    }

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->role->nom === Role::CLIENT;
    }

    /**
     * Get appointments visible to this user.
     */
    public function visibleRendezVous()
    {
        if ($this->isAdmin()) {
            return $this->rendezVous();
        }

        // For clients, only show appointments linked to their contact record
        // and belonging to their admin
        return RendezVous::where('user_id', $this->admin_user_id)
            ->where('contact_id', $this->contact_id);
    }
}
