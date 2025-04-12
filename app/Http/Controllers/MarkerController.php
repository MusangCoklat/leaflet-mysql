<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    // Menyimpan marker ke database
    public function store(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $marker = new Marker();
        $marker->lat = $request->lat;
        $marker->lng = $request->lng;
        $marker->save();

        return response()->json(['message' => 'Marker saved successfully'], 201);
    }

    // Mengambil semua marker dari database
    public function index()
    {
        $markers = Marker::all();
        return response()->json($markers);
    }

    public function destroy($id)
    {
        $marker = Marker::findOrFail($id);
        $marker->delete();
        return response()->json(['message' => 'Marker deleted successfully']);
    }

}
