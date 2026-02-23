@extends('layouts.app')

@section('content')
    <h1>Create Support Ticket</h1>

    <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
        @csrf
        <label for="subject">Subject</label>
        <input id="subject" name="subject" value="{{ old('subject') }}" required>
        @error('subject') <div class="error">{{ $message }}</div> @enderror

        <label for="priority">Priority</label>
        <select id="priority" name="priority">
            @foreach (['low', 'normal', 'high'] as $priority)
                <option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
        @error('priority') <div class="error">{{ $message }}</div> @enderror

        <label for="body">Message</label>
        <textarea id="body" name="body" rows="5" required>{{ old('body') }}</textarea>
        @error('body') <div class="error">{{ $message }}</div> @enderror

        <label for="attachment">Attachment (optional)</label>
        <input id="attachment" name="attachment" type="file" accept=".png,.jpg,.jpeg,.pdf">
        @error('attachment') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Submit</button>
    </form>
@endsection
