<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Area;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::pluck('name', 'id');
        $devices = Device::with('area')->orderBy('created_at', 'desc')->get();
        return view('pages.devices.index', compact('devices', 'areas'));
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
    public function store(StoreDeviceRequest $request)
    {
        DB::beginTransaction();
        try {
            Device::create([
                'area_id' => $request->input('area_id'),
                'name' => $request->input('name'),
                'topic' => $request->input('topic'),
                'is_active' => $request->input('is_active')
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Device created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to create device. Please try again.'])->withInput()
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
    public function edit(Device $device)
    {
        $areas = Area::pluck('name', 'id');
        return view('pages.devices.edit', compact('device', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $device = Device::findOrFail($id);

            $device->update([
                'area_id'   => $request->input('area_id'),
                'name'      => $request->input('name'),
                'topic'     => $request->input('topic'),
                'is_active' => $request->input('is_active'),
            ]);

            DB::commit();

            return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update device. Please try again.'])
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
            $user = Device::findOrFail($id);

            $user->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Device deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to delete device. Please try again.']);
        }
    }

    public function checkDeviceExists(Request $request)
    {
        $device = Device::where('name', $request->name)->first();
        if ($device) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
}
