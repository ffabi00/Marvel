<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favorites()->with(['comic', 'character'])->get();

        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'comic_id' => 'nullable|exists:comics,id',
            'character_id' => 'nullable|exists:characters,id',
        ]);

        $data['user_id'] = auth()->id();

        $favorite = Favorite::firstOrCreate($data);

        return response()->json($favorite, 201);
    }

    public function destroy($id)
    {
        $favorite = Favorite::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $favorite->delete();

        return response()->json(['message' => 'Favorite removed successfully'], 200);
    }
}