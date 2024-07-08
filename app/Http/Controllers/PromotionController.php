<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::all();
        return response()->json($promotions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'discount_percentage' => 'required|integer',
        ]);

        $promotion = Promotion::create($request->all());
        return response()->json($promotion, 201);
    }

    public function show($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['error' => 'Promotion not found'], 404);
        }
        return response()->json($promotion);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['error' => 'Promotion not found'], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'discount_percentage' => 'required|integer',
        ]);

        $promotion->update($request->all());
        return response()->json($promotion);
    }

    public function destroy($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['error' => 'Promotion not found'], 404);
        }

        $promotion->delete();
        return response()->json(['message' => 'Promotion deleted successfully']);
    }
}
