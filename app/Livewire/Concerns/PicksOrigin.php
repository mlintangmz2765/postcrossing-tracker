<?php

namespace App\Livewire\Concerns;

use App\Models\Origin;

/**
 * "Sent from" picker for sent cards: home (default), a recently used origin,
 * or a new place.
 */
trait PicksOrigin
{
    /** 'home', a saved origin id, or 'new' */
    public $origin_choice = 'home';

    public $origin_name;

    public $origin_lat;

    public $origin_lng;

    /**
     * Pre-select the origin picked earlier in this login session, so a batch
     * of cards written on a trip doesn't need re-picking. Fresh sessions
     * start at home.
     */
    protected function restoreSessionOrigin(): void
    {
        $choice = (string) session('last_origin_choice', 'home');

        $this->origin_choice = $choice !== 'home' && Origin::where('user_id', auth()->id())->whereKey((int) $choice)->exists()
            ? $choice
            : 'home';
    }

    /**
     * @return array<string, string>
     */
    protected function originRules(): array
    {
        if ($this->type !== 'sent') {
            return [];
        }

        if ($this->origin_choice === 'new') {
            return [
                'origin_name' => 'required|string|max:150',
                'origin_lat' => 'required|numeric|between:-90,90',
                'origin_lng' => 'required|numeric|between:-180,180',
            ];
        }

        return ['origin_choice' => 'required'];
    }

    /**
     * Turn the picker into an origin id (null = home), saving a new place or
     * touching a recent one.
     */
    protected function resolveOriginId(bool $rememberForSession): ?int
    {
        if ($this->type !== 'sent' || blank($this->origin_choice) || $this->origin_choice === 'home') {
            if ($rememberForSession) {
                session(['last_origin_choice' => 'home']);
            }

            return null;
        }

        if ($this->origin_choice === 'new') {
            $origin = Origin::remember(auth()->id(), (string) $this->origin_name, (float) $this->origin_lat, (float) $this->origin_lng);
        } else {
            $origin = Origin::where('user_id', auth()->id())->findOrFail((int) $this->origin_choice);
            $origin->forceFill(['last_used_at' => now()])->save();
        }

        if ($rememberForSession) {
            session(['last_origin_choice' => (string) $origin->id]);
        }

        return $origin->id;
    }
}
