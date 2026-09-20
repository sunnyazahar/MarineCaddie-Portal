<?php

namespace App\Http\Controllers;

use App\Services\OperationsDashboardService;
use Illuminate\Http\Request;

class AdministrationSearchController extends Controller
{
    public function index()
    {
        return view('administration.search');
    }

    public function lookup(Request $request, OperationsDashboardService $dashboardService)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:1000'],
        ]);

        return response()->json(
            $dashboardService->administrationLookup($request->user(), $validated['q'])
        );
    }
}
