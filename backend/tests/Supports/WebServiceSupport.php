<?php

namespace Tests\Supports;

use App\Enums\StorageProvider;

trait WebServiceSupport
{
    public function token(StorageProvider $service = StorageProvider::GOOGLE)
    {
        if ($service === StorageProvider::GOOGLE) {
            return $this->google();
        }
    }

    private function google()
    {
        return [
            'access_token' => '',
            'expires_in' => 3599,
            'refresh_token' => '',
            'scope' => '',
            'token_type' => 'Bearer',
            'created' => 1685705133,
        ];
    }
}
