<?php

namespace App\Http\Controllers;

use App\Services\OperationsDashboardService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, OperationsDashboardService $dashboardService)
    {
        $period = (int) $request->integer('period', 30);

        return view('home', [
            'dashboard' => $dashboardService->build($request->user(), $period),
        ]);
    }

    public function assistantLookup(Request $request, OperationsDashboardService $dashboardService)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:1000'],
        ]);

        return response()->json(
            $dashboardService->assistantLookup($request->user(), $validated['q'])
        );
    }
}
