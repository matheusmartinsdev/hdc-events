<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');

        if ($search) {
            $events = Event::where([
                ['title', 'like', '%' . $search . '%']
            ])->get();
        } else {
            $events = Event::all();
        }

        return view('welcome', [
            'events' => $events,
            'search' => $search
        ]);
    }

    public function create()
    {
        return view('events.create');
    }

    protected function deleteImage($imgName)
    {
        $imagePath = dirname(__FILE__, 4) . '/public/img/events/' . $imgName;

        if (!file_exists($imagePath)) {
            return true;
        } else {
            return unlink($imagePath);
        }
    }

    public function destroy($id)
    {
        $requestId = DB::table('events')
                    ->where('id', $id)
                    ->first();

        if($this->deleteImage($requestId->image)) {
            Event::findOrFail($id)->delete();
            $msg = 'Evento excluído com sucesso!';
        } else {
            $msg = 'Erro ao excluir imagem do evento!';
        }


        return redirect('/dashboard')->with('msg', $msg);
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);

        return view('events.create', ['event' => $event]);
    }

    public function update(Request $request)
    {
        Event::findOrFail($request->id)->update($request->all());

        return redirect('/dashboard')->with('msg', 'Evento atualizado com sucesso!');
    }

    public function contact()
    {
        return view('contact');
    }

    public function products()
    {
        $search = request('busca');

        return view('products', ['search' => $search]);
    }

    public function store(Request $request) 
    {
        $event = new Event;
        
        $event->title = $request->title;
        $event->city = $request->city;
        $event->date = $request->date;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Image Upload
        if ($request->hasFile('image') && $request->file('image')->isValid())
        {
            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $event->image = $imageName;
        }

        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

        $msg = "Evento criado com sucesso!";

        return redirect('/')->with('msg', $msg);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        $eventOwner = User::where('id', $event->user_id)
        ->first()
        ->toArray();
        
        return view('events.show', 
                    [
                        'event' => $event, 
                        'eventOwner' => $eventOwner
                    ]);
    }

    public function dashboard()
    {
        $user = auth()->user();

        $events = $user->events;

        return view('events.dashboard', ['events' => $events]);
    }
}
