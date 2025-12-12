@extends('crudbooster::admin_template')

@section('title', 'Tutores')


@section('content')
<div class="container">
    <h1>Detalles del Registro</h1>
    <table class="table">
        <tr>
            <th>Código Familiar:</th>
            <td>{{ $padres_tutor->codigofamiliar }}</td>
        </tr>
        <tr>
            <th>Padre:</th>
            <td>{{ optional($padres_tutor->padre)->nombre }}</td>
        </tr>
        <tr>
            <th>Madre:</th>
            <td>{{ optional($padres_tutor->madre)->nombre }}</td>
        </tr>
        <tr>
            <th>Encargado:</th>
            <td>{{ optional($padres_tutor->encargado)->nombre }}</td>
        </tr>
    </table>
    <a href="{{ route('pv_padres_tutores.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
