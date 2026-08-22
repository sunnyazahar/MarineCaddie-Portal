<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\VesselRepositoryInterface;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    public function __construct(private VesselRepositoryInterface $vessels) {}

    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));
        $vessels = $this->vessels->paginate(
            $request->only(['name', 'imo', 'type']),
            $perPage
        );

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('Vessels.partials.rows', compact('vessels'))->render(),
                'pagination' => view('partials.list-pagination-footer-inner', ['paginator' => $vessels])->render(),
                'total'      => $vessels->total(),
            ]);
        }

        $vesselTypes = $this->vessels->distinctTypes();

        return view('Vessels.index', compact('vessels', 'vesselTypes'));
    }
}
