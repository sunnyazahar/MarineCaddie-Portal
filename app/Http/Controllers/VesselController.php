<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerVessel;

class VesselController extends Controller
{
    public function index(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $imo = trim((string) $request->input('imo', ''));
        $type = trim((string) $request->input('type', ''));
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));

        $nameLike = \App\Support\ListSearch::contains($name);
        $imoLike = \App\Support\ListSearch::contains($imo);

        $vessels = CustomerVessel::query()
            ->with('customer')
            ->when($nameLike, function ($query, $pattern) {
                $query->where(function ($sub) use ($pattern) {
                    $sub->where('vessel', 'like', $pattern)
                        ->orWhere('vessel_name_alias', 'like', $pattern);
                });
            })
            ->when($imoLike, fn ($query, $pattern) => $query->where('vessel_imo', 'like', $pattern))
            ->when($type !== '', fn ($query) => $query->where('vessel_type_alias', $type))
            ->orderBy('vessel')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Vessels.partials.rows', compact('vessels'))->render(),
                'pagination' => (string) $vessels->links(),
                'total' => $vessels->total(),
            ]);
        }

        $vesselTypes = CustomerVessel::query()
            ->whereNotNull('vessel_type_alias')
            ->where('vessel_type_alias', '!=', '')
            ->distinct()
            ->orderBy('vessel_type_alias')
            ->pluck('vessel_type_alias');

        return view('Vessels.index', compact('vessels', 'vesselTypes'));
    }
}
