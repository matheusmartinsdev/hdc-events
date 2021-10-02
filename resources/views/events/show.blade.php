@extends('layouts.main')

@section('title', $event->title)

@section('content')
    <div class="container">
        <div class="row">
            <div id="image-container" class="text-center col-md-4">
                @php 
                    if($event->image) {
                        $eventImage = $event->image;
                    } else {
                        $eventImage = 'no_image_event.png';
                    }
                @endphp
                <img src="/img/events/{{ $eventImage }}" class="w-50 img-fluid" alt="{{ $event->title }}">
            </div>
            <div id="info-container" class="col-8">
                <h1>{{ $event->title }}</h1>
                <p class="event-city"><ion-icon name="location-outline"></ion-icon> {{ $event->city }}</p>
                <p class="events-participantes"><ion-icon name="people-outline"></ion-icon> X Participantes</p>
                <p class="event-owner"><ion-icon name="star-outline"></ion-icon> {{ $eventOwner['name'] }}</p>
                {{-- talvez seja possível usar $event->user->name para exibir o nome do eventOwner --}}
                <hr>
                <h3>O evento conta com:</h3>
                <ul id="items-list">
                    @foreach($event->items as $item)
                        <li>
                            <ion-icon name="play-outline"></ion-icon><span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
                
                <a href="#" class="btn btn-primary" id="event-submit">Confirmar Presença</a>
            </div>
            <div class="row">
                <div class="col-md-12" id="description-container">
                    <h3>Sobre o evento:</h3>
                    <p class="event-description">{{ $event->description }}</p>
                </div>
            </div>
        </div>
    </div>

@endsection