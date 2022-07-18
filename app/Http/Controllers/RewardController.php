<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rewards.index')->with('rewards', Reward::where('merchant_id', '=', Auth::user()->merchants()->first()->id)->get());
    }

    public function indexJson()
    {
        return response()->json(['rewards' => Reward::where('merchant_id', '=', Auth::user()->merchants()->first()->id)->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rewards.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'value' => 'required|numeric|min:1',
            'days' => 'required|numeric|min:1|max:365'
        ]);

        Reward::create([
            'merchant_id' => Auth::user()->merchants()->first()->id,
            'title' => $request->title,
            'description' => $request->description,
            'value' => $request->value,
            'photo' => 'No photo',
            'valid_until' => Carbon::now()->utc()->addDays($request->days),
        ]);

        return redirect()->route('rewards.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Reward::destroy($id);

        return redirect()->route('rewards.index');
    }
}
