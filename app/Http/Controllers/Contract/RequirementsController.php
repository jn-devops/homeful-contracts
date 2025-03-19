<?php
namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\RequirementMatrix;
use Illuminate\Http\{RedirectResponse, Request};

class RequirementsController extends Controller
{
    public function RequirementMatrix(Request $request){
        return response()->json(RequirementMatrix::all());
    }

    public function RequirementMatrixFiltered(Request $request)
    {
        $requirementMatrix = RequirementMatrix::where('civil_status', $request->civil_status ?? '')
            ->where('employment_status', $request->employment_status ?? '')
            ->get();

        // If no results found, fetch default requirements
        if ($requirementMatrix->isEmpty()) {
            $requirementMatrix = RequirementMatrix::where('civil_status', 'Single')
                ->where('employment_status', 'Locally Employed')
                ->get();
        }

        return response()->json($requirementMatrix);
    }
}
