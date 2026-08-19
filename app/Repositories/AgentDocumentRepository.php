<?php

namespace App\Repositories;

use App\Models\AgentDocument;
use App\Repositories\Contracts\AgentDocumentRepositoryInterface;

class AgentDocumentRepository extends BaseRepository implements AgentDocumentRepositoryInterface
{
    protected string $modelClass = AgentDocument::class;

    public function findOrFail(int $id): AgentDocument
    {
        return parent::findModelOrFail($id);
    }

    public function findByAgentOrFail(int $agentId, int $docId): AgentDocument
    {
        return $this->query()
            ->where('agent_id', $agentId)
            ->findOrFail($docId);
    }
}
