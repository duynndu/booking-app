<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::all();
        return response()->json($amenities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $amenity = Amenity::create($request->all());
        return response()->json($amenity, 201);
    }

    public function show($id)
    {
        $amenity = Amenity::find($id);
        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }
        return response()->json($amenity);
    }

    public function update(Request $request, $id)
    {
        $amenity = Amenity::find($id);
        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required',
            'description' => 'sometimes|required',
        ]);

        $amenity->update($request->all());
        return response()->json($amenity);
    }

    public function destroy($id)
    {
        $amenity = Amenity::find($id);
        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }
        $amenity->delete();
        return response()->json(['message' => 'Amenity deleted successfully']);
    }
}
