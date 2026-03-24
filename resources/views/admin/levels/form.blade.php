@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $level->exists ? 'Edit Level' : 'Create Level')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $level->exists ? route('admin.levels.update', $level) : route('admin.levels.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Code</label>
                        <input name="code" class="form-control" value="{{ old('code', $level->code) }}" required>
                        @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Min Deposit</label>
                        <input name="min_deposit" class="form-control" type="number" step="0.00000001" value="{{ old('min_deposit', $level->min_deposit) }}" required>
                        @error('min_deposit') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Max Deposit</label>
                        <input name="max_deposit" class="form-control" type="number" step="0.00000001" value="{{ old('max_deposit', $level->max_deposit) }}" required>
                        @error('max_deposit') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Min Reservation</label>
                        <input name="min_reservation" class="form-control" type="number" step="0.00000001" value="{{ old('min_reservation', $level->min_reservation) }}" required>
                        @error('min_reservation') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Max Reservation</label>
                        <input name="max_reservation" class="form-control" type="number" step="0.00000001" value="{{ old('max_reservation', $level->max_reservation) }}" required>
                        @error('max_reservation') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Active</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $level->is_active))>
                            <label class="form-check-label" for="is_active">Is Active</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Income Min %</label>
                        <input name="income_min_percent" class="form-control" type="number" step="0.001" value="{{ old('income_min_percent', $level->income_min_percent) }}" required>
                        @error('income_min_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Income Max %</label>
                        <input name="income_max_percent" class="form-control" type="number" step="0.001" value="{{ old('income_max_percent', $level->income_max_percent) }}" required>
                        @error('income_max_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Requirement A</label>
                        <input name="req_chain_a" class="form-control" type="number" min="0" value="{{ old('req_chain_a', $level->req_chain_a) }}">
                        @error('req_chain_a') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Requirement B</label>
                        <input name="req_chain_b" class="form-control" type="number" min="0" value="{{ old('req_chain_b', $level->req_chain_b) }}">
                        @error('req_chain_b') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Requirement C</label>
                        <input name="req_chain_c" class="form-control" type="number" min="0" value="{{ old('req_chain_c', $level->req_chain_c) }}">
                        @error('req_chain_c') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Income % A</label>
                        <input name="chain_income_a_percent" class="form-control" type="number" step="0.001" min="0" value="{{ old('chain_income_a_percent', $level->chain_income_a_percent) }}" placeholder="Leave blank for default">
                        @error('chain_income_a_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Income % B</label>
                        <input name="chain_income_b_percent" class="form-control" type="number" step="0.001" min="0" value="{{ old('chain_income_b_percent', $level->chain_income_b_percent) }}" placeholder="Leave blank for default">
                        @error('chain_income_b_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chain Income % C</label>
                        <input name="chain_income_c_percent" class="form-control" type="number" step="0.001" min="0" value="{{ old('chain_income_c_percent', $level->chain_income_c_percent) }}" placeholder="Leave blank for default">
                        @error('chain_income_c_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">{{ $level->exists ? 'Update' : 'Create' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.levels.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
