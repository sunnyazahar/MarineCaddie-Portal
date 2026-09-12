<?php

namespace App\Http\Controllers;

use App\Services\VesselLiveTrackerService;
use Illuminate\Http\Request;
use RuntimeException;

class VesselLiveTrackerController extends Controller
{
    public function __construct(private VesselLiveTrackerService $tracker) {}

    public function index(Request $request)
    {
        if ($request->has('query')) {
            return redirect()->route('vessels.live-tracker');
        }

        return view('Vessels.live-tracker', [
            'query' => '',
            'result' => null,
            'error' => null,
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->input('query', ''));
        $result = null;
        $error = null;

        if ($query !== '') {
            try {
                $result = $this->tracker->lookup($query);
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        }

        return response()->json([
            'html' => view('Vessels.partials.live-tracker-result', compact('result', 'error'))->render(),
            'has_result' => $result !== null,
            'error' => $error,
        ]);
    }

    public function suggestions(Request $request)
    {
        return response()->json([
            'suggestions' => $this->tracker->suggestions(
                trim((string) $request->input('query', ''))
            ),
        ]);
    }
}
