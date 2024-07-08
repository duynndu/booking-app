<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $banners = Banner::all();
        return response()->json($banners);
    }


    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $banner = Banner::create($request->all());
        return response()->json($banner, 201);
    }


    public function show($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }
        return response()->json($banner);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }

        $request->validate([
            'image' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $banner->update($request->all());
        return response()->json($banner);
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }

        $banner->delete();
        return response()->json(['message' => 'Banner deleted successfully']);
    }
}
