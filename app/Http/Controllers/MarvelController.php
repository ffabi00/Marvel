<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarvelController extends Controller
{
    public function getComics(Request $request)
    {
        $publicKey = env('API_MARVEL_KEY');
        $privateKey = env('API_MARVEL_SECRET');
        $timestamp = time();
        $hash = md5($timestamp . $privateKey . $publicKey);

        // Log the request parameters
        // Log::info('Requesting Marvel API with parameters:', [
        //     'ts' => $timestamp,
        //     'apikey' => $publicKey,
        //     'hash' => $hash,
        //     'limit' => $request->get('limit', 20),
        // ]);

        $response = Http::get('http://gateway.marvel.com/v1/public/comics', [
            'ts' => $timestamp,
            'apikey' => $publicKey,
            'hash' => $hash,
            'limit' => $request->get('limit', 20),
        ]);

        $comics = $response->json()['data']['results'];

        // Filter out comics with "image_not_available" thumbnail
        $filteredComics = array_values(array_filter($comics, function ($comic) {
            return !str_contains($comic['thumbnail']['path'], 'image_not_available');
        }));

        // Log the response
        // Log::info('Marvel API response:', [
        //     'response' => $filteredComics,
        //     'publicKey' => $publicKey,
        //     'privateKey' => $privateKey,
        //     'timestamp' => $timestamp,
        //     'hash' => $hash,
        // ]);

        return response()->json(['data' => ['results' => $filteredComics]]);
    }

    public function getCharacters(Request $request)
    {
        $publicKey = env('API_MARVEL_KEY');
        $privateKey = env('API_MARVEL_SECRET');
        $timestamp = time();
        $hash = md5($timestamp . $privateKey . $publicKey);

        // Log the request parameters
        // Log::info('Requesting Marvel API with parameters:', [
        //     'ts' => $timestamp,
        //     'apikey' => $publicKey,
        //     'hash' => $hash,
        //     'limit' => $request->get('limit', 20),
        // ]);

        $response = Http::get('http://gateway.marvel.com/v1/public/characters', [
            'ts' => $timestamp,
            'apikey' => $publicKey,
            'hash' => $hash,
            'limit' => $request->get('limit', 20),
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

        // Log the response
        // Log::info('Marvel API response:', [
        //     'response' => $filteredCharacters,
        //     'publicKey' => $publicKey,
        //     'privateKey' => $privateKey,
        //     'timestamp' => $timestamp,
        //     'hash' => $hash,
        // ]);

        return response()->json(['data' => ['results' => $filteredCharacters]]);
    }
}