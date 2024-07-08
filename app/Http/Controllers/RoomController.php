<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['roomType', 'amenities'])->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string',
            'max_adults' => 'required|integer',
            'max_children' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $room = Room::create($this->convertKeys($request->all()));
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::with(['roomType', 'amenities'])->find($id);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string',
            'max_adults' => 'required|integer',
            'max_children' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $room->update($request->all());
        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }

    public function availableRooms(Request $request)
    {
        $checkinDate = $request->input('checkin_date');
        $checkoutDate = $request->input('checkout_date');
        $maxAdults = $request->input('max_adults');
        $maxChildren = $request->input('max_children');

        $bookedRooms = Booking::whereBetween('checkin_date', [$checkinDate, $checkoutDate])
            ->orWhereBetween('checkout_date', [$checkinDate, $checkoutDate])
            ->pluck('room_id');

        $availableRooms = Room::whereNotIn('id', $bookedRooms)
            ->where(function ($query) use ($maxAdults, $maxChildren) {
                $query->where('max_occupancy_points', '>=', $maxAdults + ($maxChildren * 2));
            })
            ->with(['roomType', 'amenities'])
            ->get();

        return response()->json($availableRooms);
    }
}
