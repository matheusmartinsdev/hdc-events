<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    //Delete image event
    protected function deleteEventImage(string $imgName): bool
    {
        $imagePath = dirname(__FILE__, 4) . '/public/img/events/' . $imgName;

        if (!file_exists($imagePath)) {
            return true;
        } else {
            return unlink($imagePath);
        }
    }

    /**
     * @param Request $request the request with a image to be uploaded
     * 
     * @param Event $event the event to be created in 'events' table.
     * It's can be null if the method is called for update a existent Event.
     * 
     * on a new Event:
     * 
     *  @return true if everything going well
     * 
     *  @return false otherwise.
     * 
     * updating a existing Event:
     * 
     *  @return Array $data if it's a update case. 
     *  Be sure to call the update method passing the returned $data Array
     *  Example:
     *      Event::findOrFail($request->id)->update($data); View the update method on this controller.
     * 
     */
    protected function imageUpload(Request $request, Event $event = null): mixed
    {

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            if (!$event) {
                $data = $request->all();
                $data['image'] = $imageName;
                return $data;
            } else {
                $event->image = $imageName;
                return true;
            }
        }
        return false;
    }

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

    public function destroy(int $id)
    {
        $requestId = DB::table('events')
            ->where('id', $id)
            ->first();

        //Deleting the event image
        if ($this->deleteEventImage($requestId->image)) {
            Event::findOrFail($id)->delete();
            $msg = 'Evento excluído com sucesso!';
        } else {
            $msg = 'Erro ao excluir imagem do evento!';
        }


        return redirect('/dashboard')->with('msg', $msg);
    }

    public function edit(int $id)
    {
        $event = Event::findOrFail($id);

        return view('events.create', ['event' => $event]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        if ($request->image) {
            $this->deleteEventImage($request->image);
            $data = $this->imageUpload($request);
        }

        if (Event::findOrFail($request->id)->update($data)) {
            $msg = "Atualizado com sucesso";
        } else {
            $msg = "Erro ao atualizar evento";
        }

        return redirect('/dashboard')->with('msg', $msg);
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

        $user = auth()->user();
        $event->user_id = $user->id;

        $msg = ($this->imageUpload($request, $event) && $event->save()) ? "Evento criado com sucesso!" : "Erro ao criar evento!";

        return redirect('/')->with('msg', $msg);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        $eventOwner = User::where('id', $event->user_id)
            ->first()
            ->toArray();

        return view(
            'events.show',
            [
                'event' => $event,
                'eventOwner' => $eventOwner
            ]
        );
    }

    public function dashboard()
    {
        $user = auth()->user();

        $events = $user->events;

        return view('events.dashboard', ['events' => $events]);
    }
}