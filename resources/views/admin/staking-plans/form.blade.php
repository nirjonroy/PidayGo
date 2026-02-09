@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $plan->exists ? 'Edit Staking Plan' : 'Create Staking Plan')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $plan->exists ? route('admin.staking-plans.update', $plan) : route('admin.staking-plans.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="daily_rate">Daily Rate</label>
                    <input id="daily_rate" name="daily_rate" type="number" step="0.000001" class="form-control" value="{{ old('daily_rate', $plan->daily_rate) }}" required>
                    @error('daily_rate') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="duration_days">Duration Days</label>
                    <input id="duration_days" name="duration_days" type="number" class="form-control" value="{{ old('duration_days', $plan->duration_days) }}" required>
                    @error('duration_days') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="min_amount">Min Amount</label>
                    <input id="min_amount" name="min_amount" type="number" step="0.0001" class="form-control" value="{{ old('min_amount', $plan->min_amount) }}">
                    @error('min_amount') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="max_amount">Max Amount</label>
                    <input id="max_amount" name="max_amount" type="number" step="0.0001" class="form-control" value="{{ old('max_amount', $plan->max_amount) }}">
                    @error('max_amount') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="max_payout_multiplier">Max Payout Multiplier</label>
                    <input id="max_payout_multiplier" name="max_payout_multiplier" type="number" step="0.01" class="form-control" value="{{ old('max_payout_multiplier', $plan->max_payout_multiplier) }}">
                    @error('max_payout_multiplier') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="level_required">Level Required</label>
                    <input id="level_required" name="level_required" type="number" class="form-control" value="{{ old('level_required', $plan->level_required) }}">
                    @error('level_required') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.staking-plans.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
