<?php

namespace ReadmeDisplay\App\Http\Controllers;

use ReadmeDisplay\Framework\Request\Request;
use ReadmeDisplay\App\Models\Plugin;
class PluginController extends Controller
{
    public function get(Request $request, Plugin $plugin)
    {
        return $plugin->paginate($request->per_page);
    }

    public function find(Request $request, Plugin $plugin)
    {
        try {
            return $plugin->findOrFail($request->id);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Plugin not found'], 404);
        }
    }

    public function store(Request $request, Plugin $plugin)
    {
        return $plugin->create($request->all());
    }

    public function update(Request $request, Plugin $plugin)
    {
        return $plugin->update($request->id, $request->all());
    }

    public function delete(Request $request, Plugin $plugin)
    {
        return $plugin->delete($request->id);
    }
}
