<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $terminals = Terminal::pluck('name', 'id');
        $areas = Area::with('terminal')->orderBy('created_at', 'desc')->get();
        return view('pages.areas.index', compact('areas', 'terminals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        DB::beginTransaction();
        try {
            Area::create([
                'terminal_id' => $request->input('terminal_id'),
                'name' => $request->input('name'),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Area created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to create area. Please try again.'])->withInput()
                ->with('show_create_modal', true);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        $terminals = Terminal::pluck('name', 'id');
        return view('pages.areas.edit', compact('area', 'terminals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $device = Area::findOrFail($id);

            $device->update([
                'terminal_id' => $request->input('terminal_id'),
                'name'      => $request->input('name'),
            ]);

            DB::commit();

            return redirect()->route('areas.index')->with('success', 'Area updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update area. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $area = Area::findOrFail($id);

            $area->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Area deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to delete area. Please try again.']);
        }
    }
}
