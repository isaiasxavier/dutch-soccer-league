<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function getPaginationParameters(Request $request): array
    {
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);

        return ['limit' => $limit, 'offset' => $offset];
    }
}
