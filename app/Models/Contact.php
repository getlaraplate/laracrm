<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Models\User;
use Modules\Core\Overrides\Model;
use Modules\CRM\Database\Factories\ContactFactory;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Models\Pivot\Contactable;
use Override;

final class Contact extends Model
{
    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::Contacts->value;

    /**
     * The attributes that are mass assignable.
     */
    #[Override]
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    #[Override]
    protected $hidden = [
        'user_id',
        'user',
    ];

    private ?User $tempUser = null;

    /**
     * Counterparties this contact works for (pivot `contactables`).
     *
     * @return BelongsToMany<Counterparty, $this>
     */
    public function counterparties(): BelongsToMany
    {
        return $this->belongsToMany(Counterparty::class, CRMTables::Contactables->value, 'contact_id', 'counterparty_id')
            ->using(Contactable::class)
            ->withTimestamps();
    }

    /**
     * Hold a not-yet-persisted user to attach on first save (e.g. create user and contributor in one flow).
     */
    public function setTempUser(?User $user): void
    {
        $this->tempUser = $user;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(user_class());
    }

    #[Override]
    public function save(array $options = []): bool
    {
        if ($this->tempUser instanceof User && $this->tempUser->isDirty()) {
            $this->tempUser->save();
            $this->user_id = $this->tempUser->id;
            $this->tempUser = null;
            $this->load('user');
        } elseif ($this->user && $this->user->isDirty()) {
            $this->user->save();
        }

        return parent::save($options);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRules(): array
    {
        $rules = parent::getRules();
        $rules['create'] = array_merge($rules['create'], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);
        $rules['update'] = array_merge($rules['update'], [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:255'],
        ]);

        return $rules;
    }

    /**
     * @return Factory<self>
     */
    #[Override]
    protected static function newFactory(): Factory
    {
        return ContactFactory::new();
    }

    protected static function booted(): void
    {
        self::addGlobalScope(static function (Builder $query): void {
            $query->with('user');
        });
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function getCanLoginAttribute(): bool
    {
        return $this->user !== null || $this->tempUser instanceof User;
    }

    protected function isUserAttribute(): bool
    {
        return $this->getCanLoginAttribute();
    }
}
