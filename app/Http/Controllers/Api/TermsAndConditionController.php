<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
    // Create or update terms and conditions
    public function createOrUpdate(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Check if the record exists
        $termsAndCondition = TermsAndCondition::latest()->first(); // Get the latest record

        if ($termsAndCondition) {
            // Update the existing record
            $termsAndCondition->update($validated);
        } else {
            // Create a new record
            TermsAndCondition::create($validated);
        }

        return response()->json(['message' => 'Terms and conditions saved successfully']);
    }

    // Get the last created term and condition
    public function getLastTermsAndCondition()
    {
        $termsAndCondition = TermsAndCondition::latest()->first();

        if ($termsAndCondition) {
            return response()->json(['content' => $termsAndCondition->content]);
        } else {
            return response()->json(['message' => 'No terms and conditions found']);
        }
    }
}
