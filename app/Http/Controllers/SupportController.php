<?php

namespace App\Http\Controllers;

use App\Models\support;
use Illuminate\Http\Request;

class SupportController extends Controller
{

    public function index()
    {
        $supportRecord = Support::first();
        $number = $supportRecord ? $supportRecord->number : null;
        return view('pages.dashboards.support.index', compact('number'));
    }

    public function addOrUpdateNumber(Request $request)
    {
        $number = $request->input('number');
        $email = $request->input('email');  
        $support = support::updateOrCreate(
            ['number' => $number],
            ['email' => $email]  
        );

        return response()->json(['message' => 'تم إضافة / تحديث الرقم بنجاح']);
    }
}
