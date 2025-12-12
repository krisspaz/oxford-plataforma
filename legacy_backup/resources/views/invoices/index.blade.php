@extends('crudbooster::admin_template')
@section('content')
    @include('crudbooster::components.page_header')
    @include('crudbooster::components.flash_message')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ $page_title }}
                </div>

                <div class="panel-body">
                    <form action="{{ route('invoice.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label>NIT del Cliente</label>
                            <input type="text" name="nit" value="{{ old('nit') }}" class="form-control" placeholder="Ingrese el NIT">
                        </div>
                        <input type="submit" class="btn btn-primary" value="Crear Factura">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection