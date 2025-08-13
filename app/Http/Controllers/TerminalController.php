<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTerminalRequest;
use App\Http\Requests\UpdateTerminalRequest;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $terminals = Terminal::orderBy('created_at', 'desc')->get();
        return view('pages.terminals.index', compact('terminals'));
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
    public function store(StoreTerminalRequest $request)
    {
        DB::beginTransaction();
        try {
            Terminal::create([
                'name' => $request->name,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Terminal created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to create Terminal. Please try again.'])->withInput()
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
    public function edit(Terminal $terminal)
    {
        return view('pages.terminals.edit', compact('terminal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTerminalRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $device = Terminal::findOrFail($id);

            $device->update([
                'name'      => $request->input('name'),
            ]);

            DB::commit();

            return redirect()->route('terminals.index')->with('success', 'Terminal updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update terminal. Please try again.'])
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
            $terminal = Terminal::findOrFail($id);

            $terminal->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Terminal deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Failed to delete terminal. Please try again.']);
        }
    }
}
