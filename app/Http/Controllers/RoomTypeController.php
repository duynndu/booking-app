<?php
// app/Http/Controllers/Api/RoomTypeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;

class RoomTypeController extends Controller
{

    public function index()
    {
        $roomTypes = RoomType::all();
        return response()->json($this->convertKeys($roomTypes));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $roomType = RoomType::create($request->all());
        return response()->json($roomType, 201);
    }

    public function show($id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['error' => 'Room type not found'], 404);
        }
        return response()->json($this->convertKeys($roomType));
    }

    public function update(Request $request, $id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['error' => 'Room type not found'], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $roomType->update($request->all());
        return response()->json($this->convertKeys($roomType));
    }

    public function destroy($id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['error' => 'Room type not found'], 404);
        }

        $roomType->delete();
        return response()->json(['message' => 'Room type deleted successfully']);
    }
}
