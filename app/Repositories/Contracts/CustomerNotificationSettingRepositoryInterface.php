<?php

namespace App\Repositories\Contracts;

interface CustomerNotificationSettingRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerNotificationSetting;

    public function updateOrCreate(array $attributes, array $values): \App\Models\CustomerNotificationSetting;
}
