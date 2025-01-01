<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class MarvelController extends Controller
{
    public function getComics(Request $request)
    {
        $cacheKey = 'marvel_comics_' . $request->get('limit', 100);
        $cacheTTL = 3600; // 1 hora

        $comics = Cache::remember($cacheKey, $cacheTTL, function () use ($request) {
            $publicKey = env('API_MARVEL_KEY');
            $privateKey = env('API_MARVEL_SECRET');
            $timestamp = time();
            $hash = md5($timestamp . $privateKey . $publicKey);

            $response = Http::get('http://gateway.marvel.com/v1/public/comics', [
                'ts' => $timestamp,
                'apikey' => $publicKey,
                'hash' => $hash,
                'limit' => $request->get('limit', 100),
            ]);

            $comics = $response->json()['data']['results'];

            // Filter out comics with "image_not_available" thumbnail
            return array_values(array_filter($comics, function ($comic) {
                return !str_contains($comic['thumbnail']['path'], 'image_not_available');
            }));
        });

        return response()->json(['data' => ['results' => $comics]]);
    }

    public function getCharacters(Request $request)
    {
        $cacheKey = 'marvel_characters_' . $request->get('limit', 100);
        $cacheTTL = 3600; // 1 hora

        $characters = Cache::remember($cacheKey, $cacheTTL, function () use ($request) {
            $publicKey = env('API_MARVEL_KEY');
            $privateKey = env('API_MARVEL_SECRET');
            $timestamp = time();
            $hash = md5($timestamp . $privateKey . $publicKey);

            $response = Http::get('http://gateway.marvel.com/v1/public/characters', [
                'ts' => $timestamp,
                'apikey' => $publicKey,
                'hash' => $hash,
                'limit' => $request->get('limit', 100),
            ]);

            $characters = $response->json()['data']['results'];

            // Filter out characters with "image_not_available" thumbnail
            $filteredCharacters = array_values(array_filter($characters, function ($character) {
                return !str_contains($character['thumbnail']['path'], 'image_not_available');
            }));

            // If no valid characters found, return the original response
            if (empty($filteredCharacters)) {
                $filteredCharacters = $characters;
            }

            return $filteredCharacters;
        });

        return response()->json(['data' => ['results' => $characters]]);
    }

    public function getUserFavorites(Request $request)
    {
        $user = Auth::user();

        $favorites = Favorite::where('user_id', $user->id)
            ->with(['comic', 'character'])
            ->get()
            ->map(function ($favorite) {
                return [
                    'id' => $favorite->id,
                    'marvel_id' => $favorite->comic ? $favorite->comic->marvel_id : $favorite->character->marvel_id,
                    'type' => $favorite->comic ? 'comic' : 'character'
                ];
            });

        return response()->json(['data' => $favorites]);
    }
}