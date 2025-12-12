@extends('crudbooster::admin_template')

@section('content')
    <form action="{{ route('ajuste-administrativos.update', $administrativo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('administrativos.ajustes.form', ['modo' => 'Editar'])

    </form>
@endsection
