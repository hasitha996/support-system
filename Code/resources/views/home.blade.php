@extends('layouts.app')
{{-- <script src="{{ asset('js/home.js') }}"></script> --}}


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show auto-close">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show auto-close">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white">Support Tickets</div>
                    <div class="card-body">
                        <table id="ticketTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Reference Number</th>
                                    <th>Problem</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ticketDetailsModal" tabindex="-1" role="dialog" aria-labelledby="ticketDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsModalLabel">Ticket Details <p>Reference: <span
                                id="modalReferenceNumber"></span></p>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="messageForm" action="{{ route('send_message') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <p>Problem: <span id="modalProbleme"></span></p>
                                <input type="hidden" name="reference_number" id="referenceNumber" value="">
                            </div>
                            <div class="col-md-6">
                                <p>Customer Name: <span id="modalCustomerName"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p>Email: <span id="modalEmail"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p>Phone Number: <span id="modalPhoneNumber"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p>Status: <span id="modalStatus"></span></p>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger" id="closeTicket">Close Ticket</button>
                            </div>
                            <div class="col-md-6">
                                <p>Discription : <span id="modalDiscription"></span></p>
                            </div>
                            <hr>
                            <div id="messageContainer" style="max-height: 300px; overflow-y: auto;"></div>
                            <hr>
                            <input type="hidden" name="ticket_id" id="ticket_id" value="">
                            <input type="hidden" name="uemail" id="uemail" value="">

                            <div class="form-group" id="msgarea">
                                <label for="message">Message:</label>
                                <textarea name="message" id="message" class="form-control" required style="border: 1px solid #007bff;"></textarea>

                            </div>
                            <hr>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="msgbtn" class="btn btn-success ml-auto">Send Message</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#ticketTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tickets_data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'reference_number',
                        name: 'reference_number'
                    },
                    {
                        data: 'problem_title',
                        name: 'problem_title'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 0) {
                                return '<span class="text-danger">Pending</span>';
                            } else if (data === 1) {
                                return '<span class="text-success">Closed</span>';
                            }
                            return '';
                        },
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<button class="btn btn-primary view-details" data-id="' + data
                                .id + '">View Details</button>';
                        },
                    },
                ],
                columnDefs: [{
                    targets: [0],
                    visible: false, // Hide the column
                }, ],
            });

            $('#ticketTable').on('click', '.view-details', function() {
                var data = $('#ticketTable').DataTable().row($(this).parents('tr')).data();
                $('#modalReferenceNumber').text(data.reference_number);
                $('#modalProbleme').text(data.problem_title);
                $('#problem_title').text(data.problem_title);
                $('#modalCustomerName').text(data.customer_name);
                $('#modalEmail').text(data.email);
                $('#modalDiscription').text(data.problem_description);
                $('#modalPhoneNumber').text(data.phone_number);
                $('#modalStatus').text(data.status === 0 ? 'Pending' : 'Closed');
                $('#ticket_id').val(data.id);
                $('#uemail').val(data.email);
                $('#referenceNumber').val(data.reference_number);
                $('#messageContainer').empty();

                if (data.status === 0) {
                    $('#msgarea').show();
                    $('#msgbtn').show();
                    $('#closeTicket').show();
                } else {
                    $('#msgarea').hide();
                    $('#msgbtn').hide();
                    $('#closeTicket').hide();
                }
                $.ajax({
    url: "{{ route('get_messages') }}",
    type: "GET",
    data: {
        ticket_id: data.id
    },
    success: function(messages) {
        messages.forEach(function(message) {
            var messageText = '';
            if (message.is_user === 1) {
                // If the user is 1, use gray color card
                messageText += '<div class="card text-white bg-secondary user-message" style="float: right; clear: both; background-color: #ccc; margin-bottom: 10px;">';
            } else {
                // If the user is 0, use blue color card
                messageText += '<div class="card text-white bg-primary other-message" style="float: left; clear: both; background-color: #007bff; color: #fff; margin-bottom: 10px;">';
            }
            messageText += '<div class="card-body">';
            messageText += '<p class="card-text"><strong>' + message.user_name + ':</strong> ' + message.message + '</p>';
            messageText += '</div></div></div>';

            $('#messageContainer').append(messageText);
        });
    },
});




                $('#ticketDetailsModal').modal('show');
            });

            $('#closeTicket').click(function() {
                var ticketId = $('#ticket_id').val();

                $.ajax({
                    url: '/tickets/' + ticketId + '/close',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        alert('Ticket closed successfully');
                        location.reload();
                    },
                    error: function(error) {
                        alert('An error occurred while closing the ticket');
                    }
                });
            });

            $('.auto-close').delay(5000).fadeOut('slow');

            $('#closeModalButton').click(function() {
                $('#ticketDetailsModal').modal('hide');
            });
        });
    </script>
@endsection
