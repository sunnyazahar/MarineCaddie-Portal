<?php

namespace App\Repositories\Contracts;

interface AgentDocumentRepositoryInterface
{
    public function findOrFail(int $id): \App\Models\AgentDocument;

    public function findByAgentOrFail(int $agentId, int $docId): \App\Models\AgentDocument;
}
