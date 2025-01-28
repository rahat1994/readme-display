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
        $data = $request->all();
        $this->validate($data, [
            'name' => 'required',
            'slug' => 'required',
        ], [
            'name.required' => __('Name is required', 'readme-display'),
            'slug.required' => __('Slug is required', 'readme-display'),
        ]);
        return $plugin->create($request->all());
    }

    public function update(Request $request, Plugin $plugin)
    {
        $data = $request->all();

        $this->validate($data, [
            'name' => 'required',
            'slug' => 'required',
        ], [
            'name.required' => __('Name is required', 'readme-display'),
            'slug.required' => __('Name is required', 'readme-display'),
        ]);
        return $plugin->update($request->id);
    }

    public function delete($id)
    {
        try {
            return Plugin::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            return wp_send_json_error(['message' => 'Plugin not found'], 404);
        }
        // return response()->json(['message' => 'Plugin not found'], 404);
        // return $plugin->delete($request->id);
    }
}
