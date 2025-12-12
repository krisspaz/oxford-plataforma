@extends('crudbooster::admin_template')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Detalles del Usuario</h3>
    </div>

    <div class="box-body">
        <p><strong><i class="fa fa-user"></i> Nombre:</strong> {{ $usuario->name }}</p>
        <p><strong><i class="fa fa-envelope"></i> Email:</strong> {{ $usuario->email }}</p>
        <p><strong><i class="fa fa-shield"></i> Privilegio:</strong> {{ $usuario->cmsPrivilege->name ?? 'N/A' }}</p>

        @if($usuario->photo)
            <p><strong><i class="fa fa-picture-o"></i> Foto:</strong></p>
            <img src="{{ asset('storage/' . $usuario->photo) }}" alt="Foto de perfil" width="150" class="img-thumbnail">
        @endif
    </div>

    <div class="box-footer">
        <a href="{{ route('usuarios.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>
</div>
@endsection
