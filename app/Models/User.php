<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Polymorphic morph children don't get cascaded by SQL foreign keys, so we
     * delete them explicitly on Eloquent's deleting event. This enforces the
     * UML composition (User ◆── Adresse) at runtime.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $user): void {
            // The user's own addresses.
            $user->adresses()->delete();

            // The SQL cascade will delete the user's contacts, but it does not
            // fire Eloquent events on those contacts, so their addresses would
            // be orphaned. Clean them up first.
            Adresse::query()
                ->where('addressable_type', Contact::class)
                ->whereIn('addressable_id', $user->contacts()->select('id'))
                ->delete();
        });
    }

    /**
     * Always eager-load the role relationship to avoid N+1 on isAdmin()/isClient().
     */
    protected $with = ['role'];

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
        'password',
        'last_login_at',
        'password_reset_token',
        'password_reset_expires',
        'email_verification_token',
        'email_verification_expires',
        'email_verification_sent_at',
        'email_verified_at',
        'terms_accepted_at',
        'terms_accepted_version',
        'google_id',
        'apple_id',
        'provider',
        'avatar',
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
            'email_verification_expires' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
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
     * Addresses linked to this user (polymorphic).
     */
    public function adresses(): MorphMany
    {
        return $this->morphMany(Adresse::class, 'addressable');
    }

    /**
     * Convenience accessor for the user's primary address.
     */
    public function adressePrincipale(): MorphOne
    {
        return $this->morphOne(Adresse::class, 'addressable')->where('is_principale', true);
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
     * Whether this user is required to verify their email before logging in.
     * OAuth users (Google, Apple) are pre-verified by their provider.
     */
    public function requiresEmailVerification(): bool
    {
        return $this->email_verified_at === null && empty($this->provider);
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
