@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $bonus->exists ? 'Edit Chain Bonus' : 'Create Chain Bonus')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $bonus->exists ? route('admin.chain-bonuses.update', $bonus) : route('admin.chain-bonuses.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Depth (1=A, 2=B, 3=C)</label>
                        <input name="depth" class="form-control" type="number" min="1" value="{{ old('depth', $bonus->depth) }}" required>
                        @error('depth') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Percent</label>
                        <input name="percent" class="form-control" type="number" step="0.001" value="{{ old('percent', $bonus->percent) }}" required>
                        @error('percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Active</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $bonus->is_active))>
                            <label class="form-check-label" for="is_active">Is Active</label>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">{{ $bonus->exists ? 'Update' : 'Create' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.chain-bonuses.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
