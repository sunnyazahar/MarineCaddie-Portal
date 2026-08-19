<?php

namespace App\Repositories\Support;

class PaginateOptions
{
    public function __construct(
        public int $perPage = 25,
        public ?string $sortBy = null,
        public string $sortDirection = 'asc',
    ) {}

    public static function fromArray(array $input, int $defaultPerPage = 25): self
    {
        $perPage = max(10, min(100, (int) ($input['per_page'] ?? $defaultPerPage)));
        $sortBy = isset($input['sort_by']) ? (string) $input['sort_by'] : null;
        $direction = strtolower((string) ($input['sort_direction'] ?? 'asc'));
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        return new self($perPage, $sortBy, $direction);
    }
}
