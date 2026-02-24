@extends('layouts.frontend')

@section('content')
<section id="subheader" class="text-light" data-bgimage="url({{ asset('frontend/images/background/subheader.jpg') }}) top">
    <div class="center-y relative text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Create Support Ticket</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section aria-label="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data" class="form-border">
                    @csrf
                    <div class="field-set">
                        <h5>Subject</h5>
                        <input id="subject" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        @error('subject') <div class="text-danger">{{ $message }}</div> @enderror

                        <div class="spacer-20"></div>

                        <h5>Priority</h5>
                        <select id="priority" name="priority" class="form-select">
                            @foreach (['low', 'normal', 'high'] as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                        @error('priority') <div class="text-danger">{{ $message }}</div> @enderror

                        <div class="spacer-20"></div>

                        <h5>Message</h5>
                        <textarea id="body" name="body" rows="5" class="form-control" required>{{ old('body') }}</textarea>
                        @error('body') <div class="text-danger">{{ $message }}</div> @enderror

                        <div class="spacer-20"></div>

                        <h5>Attachment (optional)</h5>
                        <input id="attachment" name="attachment" type="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                        @error('attachment') <div class="text-danger">{{ $message }}</div> @enderror

                        <div class="spacer-20"></div>

                        <button type="submit" class="btn-main">Submit</button>
                        <a href="{{ route('support.index') }}" class="btn-main btn-light">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
