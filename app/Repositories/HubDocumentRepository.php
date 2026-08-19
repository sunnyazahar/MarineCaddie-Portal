<?php

namespace App\Repositories;

use App\Models\HubDocument;
use App\Models\HubPricingDocument;
use App\Repositories\Contracts\HubDocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class HubDocumentRepository implements HubDocumentRepositoryInterface
{
    public function findOrFailByType(string $type, int $id): Model
    {
        if ($type === 'pricing') {
            return HubPricingDocument::findOrFail($id);
        }

        return HubDocument::findOrFail($id);
    }
}
