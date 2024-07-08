<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Amenity;
use App\Models\RefreshToken;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks to truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Truncate tables
        Room::truncate();
        RoomType::truncate();
        User::truncate();
        RefreshToken::truncate();

        // Enable foreign key checks again
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Create room types
        $roomTypes = [
            ['name' => 'Phòng Đơn', 'description' => 'Một phòng nhỏ gọn cho một người'],
            ['name' => 'Phòng Đôi', 'description' => 'Một phòng rộng rãi cho hai người'],
            ['name' => 'Phòng Deluxe', 'description' => 'Một phòng sang trọng với các tiện nghi thêm'],
            ['name' => 'Phòng Gia Đình', 'description' => 'Một phòng có thể chứa lên đến 4 người'],
            ['name' => 'Phòng Suite', 'description' => 'Một phòng sang trọng với khu vực sinh hoạt riêng'],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }

        // Create a user
        User::factory()->create([
            'name' => 'Duynnz',
            'email' => 'duynnz@gmail.com',
            'password' => Hash::make('1111'),
        ]);

        // Create amenities
        $amenities = [
            ['name' => 'Air Conditioning', 'description' => ''],
            ['name' => 'Free Wi-Fi', 'description' => ''],
            ['name' => 'Flat-screen TV', 'description' => ''],
            ['name' => 'Minibar', 'description' => ''],
            ['name' => 'Private Bathroom', 'description' => ''],
            ['name' => 'Breakfast Included', 'description' => ''],
            ['name' => 'Gym', 'description' => ''],
            ['name' => 'Swimming Pool', 'description' => ''],
            ['name' => 'Restaurant', 'description' => ''],
            ['name' => 'Bar', 'description' => ''],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }

        // Create rooms
        for ($i = 1; $i <= 15; $i++) {
            $roomTypeId = rand(1, 5); // generate a random room type id between 1 and 5
            $room = Room::create([
                'room_type_id' => $roomTypeId,
                'area' => rand(20, 100),
                'room_number' => "Room $i",
                'max_adults' => rand(1, 5),
                'max_children' => rand(0, 3),
                'price' => rand(50, 200),
                'images' => [
                    'thumbnail' => "https://picsum.photos/200/300?random={$i}",
                    'gallery' => [
                        "https://picsum.photos/1200/600?random=$i+1",
                        "https://picsum.photos/1200/600?random=$i+2",
                        "https://picsum.photos/1200/600?random=$i+3",
                    ],
                ],
            ]);

            // Assign 3 random amenities to each room
            $roomAmenities = Amenity::all()->random(8);
            $room->amenities()->attach($roomAmenities->pluck('id'));
        }
    }
}
