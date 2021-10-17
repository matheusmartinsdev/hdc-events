@extends('layouts.main')

@section('title', 'HDC Events')

@section('content')

<div id="search-container" class="col-md-12">
    <h1>Busque um evento</h1>
    <form action="/" method="get">
        <input type="text" name="search" id="search" class="form-control" placeholder="Procurar por ...">
        <input class="btn btn-primary" type="submit" value="Procurar">
    </form>
</div>

<div id="events-container" class="col-md-12">
    @if($search)
    <h2>Buscando por: {{ $search }}</h2>
    <p class="subtitle">
        @php
        ($eventsCount = $events->count())
        @endphp
        {{ $eventsCount }}
        @if ($eventsCount > 1)
        resultados encontrados
        @else
        resultado encontrado
        @endif
    </p>
    @else
    <h2>Próximos eventos</h2>
    <p class="subtitle">veja os eventos dos próximos dias</p>
    @endif
    <div id="cards-container" class="row">
        @foreach($events as $event)
        <div class="card col-md-3">
            @php
            if($event->image) {
            $eventImage = $event->image;
            } else {
            $eventImage = 'no_image_event.png';
            }
            @endphp
            <img src="/img/events/{{ $eventImage }}" alt="{{ $event->title }}">
            <div class="card-body">
                <p class="card-date">{{ date('d/m/Y', strtotime($event->date)) }}</p>
                <h5 class="card-title">{{ $event->title }}</h5>
                <p class="card-participantes"> {{ count($event->users) }} participantes</p>
                <a href="/eventos/{{ $event->id }}" class="btn btn-primary">Saber mais</a>
            </div>
        </div>
        @endforeach
        @if($events->count() == 0 && !$search)
        <p>Não há eventos disponíveis!</p>
        @endif
    </div>
</div>

@endsection