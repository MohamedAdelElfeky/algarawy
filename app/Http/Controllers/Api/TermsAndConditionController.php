<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\TermsAndCondition;
use App\Http\Controllers\Controller;
use App\Http\Requests\TermsAndConditionRequest;

class TermsAndConditionController extends Controller
{
    public function createOrUpdate(TermsAndConditionRequest $request)
    {
        $data = $request->validated();

        TermsAndCondition::updateOrCreate([], $data);

        return response()->json(['message' => 'Terms and conditions saved successfully']);
    }

    public function getLastTermsAndCondition()
    {
        $termsAndCondition = TermsAndCondition::latest()->first();

        return $termsAndCondition
            ? response()->json(['content' => $termsAndCondition->content])
            : response()->json(['message' => 'No terms and conditions found'], 404);
    
    }
}
