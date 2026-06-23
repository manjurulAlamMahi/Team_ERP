<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Traits\AjaxResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    use AjaxResponse;


    public function get(Request $request)
    {
        $query = Event::query();

        if (!empty($request->id)) {
            $query->where('id', $request->id);
        }

        $events = $query->get();

        return response()->json($events);
    }
    // Index
    public function index()
    {
        Gate::authorize('setting_events');
        $events = Event::all()->sortBy(function ($event) {
            return Carbon::parse($event->start_date . '-2024');
        });
        return view('admin.pages.events.index', compact('events'));
    }
    // Store
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
            'message'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        try {
            Event::create($data);
            return redirect()->back()->with('success', 'New Event Added !');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong !');
        }
    }
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
            'message'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        try {
            Event::find($request->id)->update($data);
            return redirect()->back()->with('success', 'Event Updated !');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong !');
        }
    }

    public function destroy(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $event = Event::find($request->id);
        $deleted = $event->delete();

        if ($deleted) {
            return $this->success([], 'Event Deleted Successfully', 200);
        }

        return $this->error([], 'Event deletion failed. Please try again.', 500);
    }
}
