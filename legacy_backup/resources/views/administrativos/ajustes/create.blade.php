@extends('crudbooster::admin_template')

@section('content')
    <form action="{{ route('ajuste-administrativos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('administrativos.ajustes.form', ['modo' => 'Crear'])

    </form>
@endsection
