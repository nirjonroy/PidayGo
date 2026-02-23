@extends('layouts.admin-panel')

@section('page-title', 'Deposit Addresses')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.deposit-addresses.create') }}" class="btn btn-primary">Add Address</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Label</th>
              <th>Currency</th>
              <th>Chain</th>
              <th>Address</th>
              <th>Status</th>
              <th>Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($addresses as $addr)
              <tr>
                <td>{{ $addr->label ?? '-' }}</td>
                <td>{{ $addr->currency }}</td>
                <td>{{ $addr->chain }}</td>
                <td style="max-width:220px; word-break:break-all;">
                  {{ \Illuminate\Support\Str::limit($addr->address, 28) }}
                </td>
                <td>
                  @if ($addr->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $addr->updated_at }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.deposit-addresses.edit', $addr) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  @if (!$addr->is_active)
                    <form method="POST" action="{{ route('admin.deposit-addresses.activate', $addr) }}" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-success" type="submit">Activate</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.deposit-addresses.deactivate', $addr) }}" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-outline-secondary" type="submit">Deactivate</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">No deposit addresses yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
