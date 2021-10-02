@extends('layouts.main')

@if($event ?? '')

    @section('title', 'Editando: ' . $event->title )

@else

    @section('title', 'Criar evento')

@endif

@section('content')
    <div id="event-create-container" class="col-md-6 offset-md-3">
        <h1>Crie seu evento</h1>
        <form 
            @if($event ?? '')
            action="/eventos/atualizar/{{$event->id}}"
            @else
            action="/eventos"
            @endif
            method="post"
            enctype="multipart/form-data"
        >
            @csrf
            @if($event ?? '')
            @method('PUT')
            @endif
            <div class="form-group">
                <label for="image">Imagem do evento: </label>
                <input type="file" id="image" name="image" class="from-control-file">
                @if($event ?? '')
                <img src="/img/events/{{ $event->image }}" alt="{{ $event->title }}" class="img-preview">
                @endif
            </div>
            <div class="form-group">
                <label for="title">Evento: </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    class="form-control" 
                    placeholder="Nome do evento"
                    @if($event ?? '')
                    value="{{ $event->title }}"
                    @endif
                >
            </div>
            <div class="form-group">
                <label for="date">Período:</label>
                <input 
                    type="date"
                    name="date"
                    id="date"
                    class="form-control"
                    @if($event ?? '')
                    value="{{ $event->date->format('Y-m-d') }}"
                    @endif
                >
            </div>
            <div class="form-group">
                <label for="title">Cidade: </label>
                <input 
                    type="text"
                    name="city"
                    id="city"
                    class="form-control"
                    placeholder="Local do evento"
                    @if($event ?? '')
                    value="{{ $event->city }}"
                    @endif
                >
            </div>
            <div class="form-group">
                <label for="title">Privado? </label>
                <select name="private" id="private" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Descrição: </label>
                <textarea name="description" id="description" class="form-control" placeholder="Descrição do evento">@if($event ?? ''){{$event->description}}@endif</textarea>
            </div>
            <div class="form-group">
                <label for="title">Adicione itens de intraestrutura:</label>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Cadeiras">Cadeiras</input>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Palco">Palco</input>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Cerveja grátis">Cerveja grátis</input>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Open Food">Open Food</input>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Brindes">Brindes</input>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Criar Evento">
        </form>
    </div>
@endsection