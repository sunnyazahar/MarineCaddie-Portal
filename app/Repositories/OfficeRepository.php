<?php

namespace App\Repositories;

use App\Models\Office;
use App\Repositories\Contracts\OfficeRepositoryInterface;
use Illuminate\Support\Collection;

class OfficeRepository implements OfficeRepositoryInterface
{
    public function all(): Collection
    {
        return Office::with('country')->get();
    }

    public function findOrFail(int $id): Office
    {
        return Office::findOrFail($id);
    }
}
