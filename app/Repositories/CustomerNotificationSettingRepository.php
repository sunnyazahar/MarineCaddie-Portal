<?php

namespace App\Repositories;

use App\Models\CustomerNotificationSetting;
use App\Repositories\Contracts\CustomerNotificationSettingRepositoryInterface;

class CustomerNotificationSettingRepository extends BaseRepository implements CustomerNotificationSettingRepositoryInterface
{
    protected string $modelClass = CustomerNotificationSetting::class;

    public function create(array $data): CustomerNotificationSetting
    {
        return parent::create($data);
    }

    public function updateOrCreate(array $attributes, array $values): CustomerNotificationSetting
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }
}
