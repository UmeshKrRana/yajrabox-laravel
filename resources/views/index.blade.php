@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Search Filter --}}
        <div class="row mb-3">
            <div class="col-xl-6 d-flex align-items-center">
                <div class="form-group w-100">
                    <label for="start-date">Start Date </label>
                    <input type="date" class="form-control" id="start-date" />
                </div>

                <div class="form-group w-100 ms-3">
                    <label for="end-date">End Date </label>
                    <input type="date" class="form-control" id="end-date" />
                </div>
            </div>
        </div>

        <div class="card bg-dark text-white">
            <div class="card-header">Manage Users</div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table table-dark table-striped']) }}
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
