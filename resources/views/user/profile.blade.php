@extends('crudbooster::admin_template')

@section('content')
<div class="container">
    <h2>Perfil de Usuario</h2>
    
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">Información del Usuario</h3>
        </div>
        <div class="box-body">
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Fecha de Creación:</strong> {{ $user->created_at }}</p>
            <p><strong>Última Actualización:</strong> {{ $user->updated_at }}</p>
        </div>
    </div>
</div>
@endsection

