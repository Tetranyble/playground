<?php

namespace Tests\Supports;

use App\Enums\Disk;

trait WebServiceSupport
{
    public function token(Disk $service = Disk::GOOGLE)
    {
        if ($service === Disk::GOOGLE) {
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
