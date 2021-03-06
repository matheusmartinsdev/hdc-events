<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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
     *  @return array|bool if everything going well
     *
     *  @return false otherwise.
     *
     * updating a existing Event:
     *
     *  @return Array $data if it's a update case.
     *  Be sure to call the update method passing the returned $data Array
     *  Example:
     *      Event::findOrFail($request->id)->update($data); See the update method on this controller.
     *
     */
    protected function imageUpload(Request $request, Event $event = null): array|bool
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

    public function index(): View
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

    public function create(): View
    {
        return view('events.create');
    }

    public function destroy(int $id): RedirectResponse
    {
        $eventId = Event::findOrFail($id);

        //Deleting the event image
        if ($this->deleteEventImage($eventId->image)) {
            $eventId->delete();
            $msg = 'Evento exclu??do com sucesso!';
        } else {
            $msg = 'Erro ao excluir imagem do evento!';
        }


        return redirect('/dashboard')->with('msg', $msg);
    }

    public function edit(int $id): View|RedirectResponse
    {
        $user = auth()->user();

        $event = Event::findOrFail($id);

        if ($user->id != $event->user->id) {
            return redirect('/dashboard');
        }

        return view('events.create', ['event' => $event]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->all();
        $event = Event::findOrFail($request->id);

        if ($request->image) {
            $this->deleteEventImage($event->image);
            $data = $this->imageUpload($request);
        }

        if ($event->update($data)) {
            $msg = "Atualizado com sucesso";
        } else {
            $msg = "Erro ao atualizar evento";
        }

        return redirect('/dashboard')->with('msg', $msg);
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function products(): View
    {
        $search = request('busca');

        return view('products', ['search' => $search]);
    }

    public function store(Request $request): RedirectResponse
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

    public function show($id): View
    {
        $event = Event::findOrFail($id);

        $user = auth()->user();
        $hasUserJoined = false;

        if ($user) {
            $userEvents = $user->eventsAsParticipant->toArray();
            foreach ($userEvents as $userEvent) {
                if ($userEvent['id'] == $id) {
                    $hasUserJoined = true;
                }
            }
        }

        $eventOwner = User::where('id', $event->user_id)
            ->first()
            ->toArray();

        return view(
            'events.show',
            [
                'event' => $event,
                'eventOwner' => $eventOwner,
                'hasUserJoined' => $hasUserJoined
            ]
        );
    }

    public function dashboard(): View
    {
        $user = auth()->user();

        $events = $user->events;

        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard', [
            'events' => $events,
            'eventsAsParticipant' => $eventsAsParticipant
        ]);
    }

    public function joinEvent(int $id): RedirectResponse
    {
        $user = auth()->user();

        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presen??a est?? confirmada em ' . $event->title);
    }

    public function leaveEvent(int $id)
    {
        $user = auth()->user();

        $user->eventsAsParticipant()->detach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Voc?? saiu do evento ' . $event->title . ' com sucesso!');
    }
}