@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $plan->exists ? 'Edit Reserve Plan' : 'Create Reserve Plan')

    @php
        $rangeRows = old('ranges');

        if ($rangeRows === null) {
            $rangeRows = $plan->relationLoaded('ranges')
                ? $plan->ranges->map(fn ($range) => [
                    'id' => $range->id,
                    'wallet_balance_min' => $range->wallet_balance_min,
                    'wallet_balance_max' => $range->wallet_balance_max,
                    'reserve_percentage' => $range->reserve_percentage,
                ])->values()->all()
                : [];
        }

        if (empty($rangeRows) && ($plan->wallet_balance_min !== null || $plan->wallet_balance_max !== null || $plan->reserve_amount !== null)) {
            $rangeRows = [[
                'id' => null,
                'wallet_balance_min' => $plan->wallet_balance_min,
                'wallet_balance_max' => $plan->wallet_balance_max,
                'reserve_percentage' => $plan->reserve_amount,
            ]];
        }

        if (empty($rangeRows)) {
            $rangeRows = [[
                'id' => null,
                'wallet_balance_min' => '',
                'wallet_balance_max' => '',
                'reserve_percentage' => '',
            ]];
        }
    @endphp

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $plan->exists ? route('admin.reserve-plans.update', $plan) : route('admin.reserve-plans.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Profit Min %</label>
                        <input name="profit_min_percent" class="form-control" type="number" step="0.001" value="{{ old('profit_min_percent', $plan->profit_min_percent) }}" required>
                        @error('profit_min_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Profit Max %</label>
                        <input name="profit_max_percent" class="form-control" type="number" step="0.001" value="{{ old('profit_max_percent', $plan->profit_max_percent) }}" required>
                        @error('profit_max_percent') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Sells (per reserve)</label>
                        <input name="max_sells" class="form-control" type="number" min="1" value="{{ old('max_sells', $plan->max_sells) }}" placeholder="Leave empty for unlimited">
                        @error('max_sells') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Sells Per Day</label>
                        <input name="max_sells_per_day" class="form-control" type="number" min="1" value="{{ old('max_sells_per_day', $plan->max_sells_per_day) }}" placeholder="Leave empty for unlimited">
                        @error('max_sells_per_day') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Unlock Policy</label>
                        <select name="unlock_policy" class="form-select" required>
                            @php($selectedPolicy = old('unlock_policy', $plan->unlock_policy ?? 'never'))
                            <option value="never" @selected($selectedPolicy === 'never')>Never unlock</option>
                            <option value="after_sells" @selected($selectedPolicy === 'after_sells')>Unlock after max sells</option>
                            <option value="manual" @selected($selectedPolicy === 'manual')>Manual unlock (admin)</option>
                        </select>
                        @error('unlock_policy') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reserve Criteria</label>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                Add as many reserve criteria rows as needed. Each row defines the level amount band and the reserve percentage for that band.
                            </div>
                            <button class="btn btn-sm btn-outline-primary" type="button" id="add-reserve-range">Add Row</button>
                        </div>

                        <div id="reserve-ranges" data-next-index="{{ count($rangeRows) }}">
                            @foreach ($rangeRows as $index => $row)
                                <div class="border rounded p-3 mb-3 reserve-range-row" data-range-row>
                                    <input type="hidden" name="ranges[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Level Amount From</label>
                                            <input name="ranges[{{ $index }}][wallet_balance_min]" class="form-control" type="number" step="0.00000001" min="0" value="{{ $row['wallet_balance_min'] }}" required>
                                            @error("ranges.$index.wallet_balance_min") <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Level Amount To</label>
                                            <input name="ranges[{{ $index }}][wallet_balance_max]" class="form-control" type="number" step="0.00000001" min="0" value="{{ $row['wallet_balance_max'] }}" required>
                                            @error("ranges.$index.wallet_balance_max") <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Reserve Percentage</label>
                                            <input name="ranges[{{ $index }}][reserve_percentage]" class="form-control" type="number" step="0.001" min="0.001" max="100" value="{{ $row['reserve_percentage'] }}" required>
                                            @error("ranges.$index.reserve_percentage") <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-1 mb-2">
                                            <button class="btn btn-outline-danger w-100 remove-reserve-range" type="button">X</button>
                                        </div>
                                    </div>
                                    @error("ranges.$index.id") <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>

                        @error('ranges') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('reserve-ranges');
        var addButton = document.getElementById('add-reserve-range');

        if (!container || !addButton) {
            return;
        }

        function nextIndex() {
            var current = parseInt(container.getAttribute('data-next-index') || '0', 10);
            container.setAttribute('data-next-index', String(current + 1));
            return current;
        }

        function rowTemplate(index) {
            return '' +
                '<div class="border rounded p-3 mb-3 reserve-range-row" data-range-row>' +
                    '<input type="hidden" name="ranges[' + index + '][id]" value="">' +
                    '<div class="row align-items-end">' +
                        '<div class="col-md-4 mb-2">' +
                            '<label class="form-label">Level Amount From</label>' +
                            '<input name="ranges[' + index + '][wallet_balance_min]" class="form-control" type="number" step="0.00000001" min="0" required>' +
                        '</div>' +
                        '<div class="col-md-4 mb-2">' +
                            '<label class="form-label">Level Amount To</label>' +
                            '<input name="ranges[' + index + '][wallet_balance_max]" class="form-control" type="number" step="0.00000001" min="0" required>' +
                        '</div>' +
                        '<div class="col-md-3 mb-2">' +
                            '<label class="form-label">Reserve Percentage</label>' +
                            '<input name="ranges[' + index + '][reserve_percentage]" class="form-control" type="number" step="0.001" min="0.001" max="100" required>' +
                        '</div>' +
                        '<div class="col-md-1 mb-2">' +
                            '<button class="btn btn-outline-danger w-100 remove-reserve-range" type="button">X</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        }

        addButton.addEventListener('click', function () {
            container.insertAdjacentHTML('beforeend', rowTemplate(nextIndex()));
        });

        container.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-reserve-range')) {
                return;
            }

            var rows = container.querySelectorAll('[data-range-row]');
            if (rows.length <= 1) {
                return;
            }

            var row = event.target.closest('[data-range-row]');
            if (row) {
                row.remove();
            }
        });
    });
</script>
@endpush
