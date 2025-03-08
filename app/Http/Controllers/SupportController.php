<?php

namespace App\Http\Controllers;

use App\Domain\Models\support;
use Illuminate\Http\Request;

class SupportController extends Controller
{

    public function index()
    {
        $supportRecord = support::first();
        $number = $supportRecord ? $supportRecord->number : null;
        $email = $supportRecord ? $supportRecord->email : null;
        return view('pages.dashboards.support.index', compact('number', 'email'));
    }

    public function addOrUpdateNumber(Request $request)
    {
        $number = $request->input('number');
        $email = $request->input('email');

        // Delete all existing records to ensure only one record exists
        Support::truncate();

        // Create a new record with the provided data
        Support::create([
            'number' => $number,
            'email' => $email,
        ]);

        return redirect()->route('support')->with('success', 'تم إضافة / تحديث الرقم بنجاح');
    }
}
