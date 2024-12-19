<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function fetch(Request $request)
    {
        $letter = $request->input('letter', 'A'); // Default to 'A'
        $clients = Client::where('name', 'LIKE', "$letter%")
            ->orderBy('name')
            ->paginate(50); // 50 clients per page

        return response()->json($clients);
    }
}
