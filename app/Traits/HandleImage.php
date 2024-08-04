<?php

namespace App\Traits;

use App\Models\Image;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandleImage
{
    /**
     * @param Request $request
     * @param string $imageableType
     * @return \Illuminate\Http\JsonResponse
     */
    private function uploadImage($instance, Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $image = $request->file('file');
        $filenameWithoutExtension = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $limitedFilename = substr($filenameWithoutExtension, 0, 20);
        $uuid = Str::uuid();
        $filename = "{$uuid}-{$limitedFilename}.{$image->getClientOriginalExtension()}";
        $path = $image->storeAs('', $filename, 'public');
        $url = asset('storage/' . $path);

        $instance->images()->create([
            'url' => $url,
        ]);
        return response()->json(['url' => $url], 201);
    }

    public function deleteImages($imagesInstance)
    {
        foreach ($imagesInstance as $image) {
            try {
                Storage::delete('/public/' . basename($image->url));
                $image->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete image'
                ]);
            }
        }
    }
}
