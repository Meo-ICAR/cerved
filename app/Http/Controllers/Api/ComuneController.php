<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ComuneController extends Controller
{
    /**
     * Display a listing of the comuni.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'province_code' => 'nullable|string|size:2',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Build the query
        $query = Comune::query();

        // Filter by province code if provided
        if ($request->has('province_code')) {
            $query->where('province_code', $request->province_code);
        }

        // Search in comune name if search term is provided
        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('municipality_description', 'like', $searchTerm);
        }

        // Pagination
        $perPage = $request->per_page ?? 20;
        $comuni = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $comuni->items(),
            'pagination' => [
                'total' => $comuni->total(),
                'per_page' => $comuni->perPage(),
                'current_page' => $comuni->currentPage(),
                'last_page' => $comuni->lastPage(),
                'from' => $comuni->firstItem(),
                'to' => $comuni->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified comune.
     *
     * @param  string  $istatCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($istatCode)
    {
        $comune = Comune::where('istat_code_municipality', $istatCode)
                       ->orWhere('belfiore_code', $istatCode)
                       ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $comune
        ]);
    }
}
