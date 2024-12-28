<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Models\Character;
use App\Models\Comic;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favorites()->with(['comic', 'character'])->get();
        return response()->json($favorites);
    }

    public function storeCharacter(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'required|array',
            'thumbnail.path' => 'required|string',
            'thumbnail.extension' => 'required|string',
            'marvel_id' => 'required|integer',
            'comics_available' => 'required|integer',
            'series_available' => 'required|integer',
            'stories_available' => 'required|integer',
            'events_available' => 'required|integer',
        ]);

        $data['user_id'] = auth()->id();

        $character = Character::updateOrCreate(
            ['marvel_id' => $data['marvel_id']],
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'thumbnail' => json_encode($data['thumbnail']),
                'comics_available' => $data['comics_available'],
                'series_available' => $data['series_available'],
                'stories_available' => $data['stories_available'],
                'events_available' => $data['events_available'],
            ]
        );

        $favorite = Favorite::firstOrCreate([
            'user_id' => $data['user_id'],
            'character_id' => $character->id,
        ]);

        return response()->json($favorite, 201);
    }

    public function storeComic(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'required|array',
            'thumbnail.path' => 'required|string',
            'thumbnail.extension' => 'required|string',
            'marvel_id' => 'required|integer',
            'page_count' => 'required|integer',
            'onsale_date' => 'required|date',
            'price' => 'required|numeric',
            'creators' => 'nullable|array',
            'characters' => 'nullable|array',
            'stories' => 'nullable|array',
        ]);

        $data['user_id'] = auth()->id();

        // Convertendo a data para o formato YYYY-MM-DD
        $data['onsale_date'] = date('Y-m-d', strtotime($data['onsale_date']));

        $comic = Comic::updateOrCreate(
            ['marvel_id' => $data['marvel_id']],
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'thumbnail' => json_encode($data['thumbnail']),
                'pages' => $data['page_count'], // Mapeando page_count para pages
                'publication_date' => $data['onsale_date'], // Mapeando onsale_date para publication_date
                'price' => $data['price'],
                'creators' => json_encode($data['creators'] ?? []),
                'characters' => json_encode($data['characters'] ?? []),
                'stories' => json_encode($data['stories'] ?? []),
            ]
        );

        $favorite = Favorite::firstOrCreate([
            'user_id' => $data['user_id'],
            'comic_id' => $comic->id,
        ]);

        return response()->json($favorite, 201);
    }

    public function destroy($type, $id)
    {
        if ($type === 'character') {
            $favorite = Favorite::where('character_id', $id)->where('user_id', auth()->id())->firstOrFail();
        } else {
            $favorite = Favorite::where('comic_id', $id)->where('user_id', auth()->id())->firstOrFail();
        }
        $favorite->delete();

        return response()->json(['message' => 'Favorite removed successfully'], 200);
    }
}
