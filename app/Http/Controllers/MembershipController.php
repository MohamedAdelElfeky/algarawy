<?php

namespace App\Http\Controllers;

use App\Domain\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::paginate(25);
        return view('pages.dashboards.memberships.index', compact('memberships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'points_required' => 'required|integer|min:0',
            'benefits' => 'nullable|string',
        ]);

        Membership::create($validated);

        return response()->json(['message' => 'تمت إضافة العضوية بنجاح .']);
    }


    public function destroy(Membership $membership)
    {
        $membership->delete();
        return response()->json(['message' => 'تم حذف العضوية بنجاح!']);
    }
}
