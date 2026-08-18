<?php

namespace App\Support;

class ListSearch
{
    /**
     * Prefix LIKE pattern for indexed ID/code columns on large tables.
     *
     * Leading-wildcard LIKE '%term%' cannot use B-tree indexes and will
     * table-scan at 10-crore row scale. `term%` can use the index.
     * Returns null when the term is too short to be selective.
     */
    public static function prefix(string $term, int $minLength = 3): ?string
    {
        $term = trim($term);
        if ($term === '' || mb_strlen($term) < $minLength) {
            return null;
        }

        return addcslashes($term, "%_\\") . '%';
    }

    /**
     * Contains LIKE for small master-data lists (hubs, agents, vessels, …).
     * Do not use on crrs/shipments at 10-crore scale.
     */
    public static function contains(string $term, int $minLength = 1): ?string
    {
        $term = trim($term);
        if ($term === '' || mb_strlen($term) < $minLength) {
            return null;
        }

        return '%' . addcslashes($term, "%_\\") . '%';
    }
}
