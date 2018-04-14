<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * GET /client/{id?}
     */
    public function get($id = 0)
    {
        if ($id) {
            $cs = Client::findOrFail($id);
        } else {
            $cs = Client::all();
        }
        return response()->api($cs);
    }

    /**
     * POST /client
     */
    public function create(Request $request)
    {
        $request->validate([
            'cname' => 'required|min:1|max:50|unique:tenant.clients',
        ], [
            'cname.required' => "Please enter a name for the client",
            'cname.unique' => "A client with the same name aleady exists.",
        ]);

        $c = new Client;
        $c->cname = $request->cname;
        $c->save();

        return response()->api([
            'data' => $c,
            'message' => "Client saved.",
        ], 201);
    }

    /**
     * PUT /client/{id}
     */
    public function update(Request $request, $id)
    {
        $c = Client::findOrFail($id);

        $request->validate([
            'cname' => "required|min:1|max:50|unique:tenant.clients,cname,$id,id",
        ], [
            'cname.required' => "Please enter a name for the client",
            'cname.unique' => "A client with the same name aleady exists.",
        ]);

        $c->cname = $request->cname;
        $c->save();

        return response()->api([
            'data' => $c,
            'message' => "Client saved.",
        ]);
    }

    /**
     * DELETE /client/{id}
     */
    public function delete($id)
    {
        Client::destroy($id);
        return response()->api([
            'message' => "Client deleted.",
        ]);
    }
}
