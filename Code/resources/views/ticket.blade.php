@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @if (session('success'))
                <div class="alert alert-success auto-close">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger auto-close">
                    {{ session('error') }}
                </div>
            @endif

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header  bg-primary">{{ __('Open Ticket') }}</div>

                    <div class="card-body">
                        <form action="{{ route('store_ticket') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="problem_title" class="form-label">Problem</label>
                                <textarea name="problem_title" id="problem_title" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="problem_description" class="form-label">Problem Description</label>
                                <textarea name="problem_description" id="problem_description" class="form-control" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                            </div>

                            <div class="text-end"> <!-- Use text-end class to align content to the right -->
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.auto-close').delay(5000).fadeOut('slow');
        });
    </script>
@endsection
