<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckRole;
use App\Models\Booking;
use App\Models\Image;
use App\Traits\HandleImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    use HandleImage;

    public function __construct()
    {
        $this->middleware('admin', ['only' => ['store', 'update', 'destroy', 'uploadImageRoom', 'createRoom']]);
        $this->middleware('staff', ['only' => ['store', 'update', 'uploadImageRoom', 'createRoom']]);
    }

    public function index()
    {
        $rooms = Room::with(['images', 'roomType', 'amenities'])->where('status', 'published')->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string',
            'max_adults' => 'required|integer',
            'max_children' => 'required|integer',
            'area' => 'required|integer',
            'price' => 'required|integer',
            'images' => 'array',
        ]);

        $room = new Room();
        $room->room_type_id = $request->input('room_type_id');
        $room->room_number = $request->input('room_number');
        $room->max_adults = $request->input('max_adults');
        $room->max_children = $request->input('max_children');
        $room->area = $request->input('area');
        $room->price = $request->input('price');
        $room->save();

        if ($request->has('images')) {
            $images = $request->input('images');
            foreach ($images as $image) {
                $newImage = new Image(['url' => $image]);
                $room->images()->save($newImage);
            }
        }

        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::with(['images', 'roomType', 'amenities'])->find($id);
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
            'amenity_ids' => 'array', // Thêm validation cho amenity_ids
        ]);

        $room->room_type_id = $request->input('room_type_id');
        $room->room_number = $request->input('room_number');
        $room->max_adults = $request->input('max_adults');
        $room->max_children = $request->input('max_children');
        $room->price = $request->input('price');
        $room->status = 'published';

        $room->save();

        $amenityIds = $request->input('amenity_ids');
        if ($amenityIds) {
            $room->amenities()->sync($amenityIds);
        }

        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        $this->deleteImages($room->images);
        $room->amenities()->detach();
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }

    public function availableRooms(Request $request)
    {
        $this->middleware('auth:api');
        $request->validate([
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date',
        ]);

        $checkinDate = Carbon::parse($request->checkin_date);
        $checkoutDate = Carbon::parse($request->checkout_date);
        $maxAdults = $request->max_adults ?? 0;
        $maxChildren = $request->max_children ?? 0;

        $bookedRooms = Booking::whereBetween('checkin_date', [$checkinDate, $checkoutDate])
            ->orWhereBetween('checkout_date', [$checkinDate, $checkoutDate])
            ->pluck('room_id');
//        return response()->json($bookedRooms);
        $availableRooms = Room::whereNotIn('id', $bookedRooms)
            ->where(function ($query) use ($maxAdults, $maxChildren) {
                $query->where('max_occupancy_points', '>=', $maxAdults + ($maxChildren * 2));
            })
            ->with(['images', 'roomType', 'amenities'])
            ->get();

        return response()->json($availableRooms);
    }

    public function createRoom()
    {
        $existingDefaultRoom = Room::where('status', 'draft')->with(['images', 'roomType', 'amenities'])->first();
        if ($existingDefaultRoom) {
            return response()->json($existingDefaultRoom);
        }
        $defaultRoom = new Room([
            'room_type_id' => 1,
            'status' => 'draft',
        ]);
        $defaultRoom->save();
        return response()->json($defaultRoom);
    }

    public function uploadImageRoom(Room $id, Request $request)
    {
        return $this->uploadImage($id, $request);
    }
}
