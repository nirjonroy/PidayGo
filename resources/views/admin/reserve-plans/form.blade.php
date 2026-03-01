@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $plan->exists ? 'Edit Reserve Plan' : 'Create Reserve Plan')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $plan->exists ? route('admin.reserve-plans.update', $plan) : route('admin.reserve-plans.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Level</label>
                        <select name="level_id" class="form-select" required>
                            <option value="">Select level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" @selected(old('level_id', $plan->level_id) == $level->id)>
                                    {{ $level->code }} ({{ $level->min_deposit ?? $level->min_reservation }} - {{ $level->max_deposit ?? $level->max_reservation }})
                                </option>
                            @endforeach
                        </select>
                        @error('level_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Reserve Amount</label>
                        <input name="reserve_amount" class="form-control" type="number" step="0.00000001" value="{{ old('reserve_amount', $plan->reserve_amount) }}" required>
                        @error('reserve_amount') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Profit Min %</label>
                        <input name="profit_min_percent" class="form-control" type="number" step="0.001" value="{{ old('profit_min_percent', $plan->profit_min_percent) }}" required>
                        @error('profit_min_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Profit Max %</label>
                        <input name="profit_max_percent" class="form-control" type="number" step="0.001" value="{{ old('profit_max_percent', $plan->profit_max_percent) }}" required>
                        @error('profit_max_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Max Sells (per reserve)</label>
                        <input name="max_sells" class="form-control" type="number" min="1" value="{{ old('max_sells', $plan->max_sells) }}" placeholder="Leave empty for unlimited">
                        @error('max_sells') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Unlock Policy</label>
                        <select name="unlock_policy" class="form-select" required>
                            @php($selectedPolicy = old('unlock_policy', $plan->unlock_policy ?? 'never'))
                            <option value="never" @selected($selectedPolicy === 'never')>Never unlock</option>
                            <option value="after_sells" @selected($selectedPolicy === 'after_sells')>Unlock after max sells</option>
                            <option value="manual" @selected($selectedPolicy === 'manual')>Manual unlock (admin)</option>
                        </select>
                        @error('unlock_policy') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Active</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $plan->is_active))>
                            <label class="form-check-label" for="is_active">Is Active</label>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">{{ $plan->exists ? 'Update' : 'Create' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.reserve-plans.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
