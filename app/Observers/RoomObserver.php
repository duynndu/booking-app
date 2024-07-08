<?php

namespace App\Observers;

use App\Models\Room;

class RoomObserver
{
    /**
     * Calculate the max occupancy points based on max adults and max children.
     */
    private function calculateMaxOccupancyPoints(Room $room): int
    {
        return ($room->max_adults * 2) + $room->max_children;
    }

    /**
     * Handle the Room "created" event.
     */
    public function created(Room $room): void
    {
        $room->max_occupancy_points = $this->calculateMaxOccupancyPoints($room);
        $room->saveQuietly();
    }

    /**
     * Handle the Room "updated" event.
     */
    public function updated(Room $room): void
    {
        $room->max_occupancy_points = $this->calculateMaxOccupancyPoints($room);
        $room->saveQuietly();
    }
}
