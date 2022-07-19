<?php

namespace NotificationChannels\ExpoPushNotifications\Repositories;

use ExponentPhpSDK\Dto\Token;
use ExponentPhpSDK\ExpoRepository;
use NotificationChannels\ExpoPushNotifications\Models\Interest;

class ExpoDatabaseDriver implements ExpoRepository
{
    /**
     * Stores an Expo token with a given identifier.
     */
    public function store(
        string $key,
        string $value,
        ?string $experienceId = null,
    ): bool {
        $interest = Interest::firstOrCreate([
            'experience_id' => $experienceId,
            'key' => $key,
            'value' => $value,
        ]);

        return $interest instanceof Interest;
    }

    /**
     * Retrieves an Expo token with a given identifier.
     *
     * @return array<Token>
     */
    public function retrieve(string $key): array
    {
        return Interest::where('key', $key)
            ->get()
            ->map(fn (Interest $interest) => new Token(
                $interest->value,
                $interest->experience_id
            ))
            ->toArray();
    }

    /**
     * Removes an Expo token with a given identifier.
     */
    public function forget(string $key, ?string $value = null): bool
    {
        $query = Interest::where('key', $key);

        if ($value) {
            $query->where('value', $value);
        }

        return $query->delete() > 0;
    }
}
