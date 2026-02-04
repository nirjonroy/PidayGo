@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Dashboard')

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pending KYC</h5>
                    <p class="card-text">Review user submissions.</p>
                    <a href="{{ route('admin.kyc.index') }}" class="btn btn-primary">View KYC</a>
                </div>
            </div>
        </div>
    </div>
@endsection
