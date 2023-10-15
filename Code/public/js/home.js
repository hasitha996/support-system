$(document).ready(function () {
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
            render: function (data, type, row) {
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
            render: function (data, type, row) {
                return '<button class="btn btn-primary view-details" data-id="' + data
                    .id + '">View Details</button>';
            },
        },
        ],
        columnDefs: [{
            targets: [0],
            visible: false, // Hide the column
        },],
    });

    $('#ticketTable').on('click', '.view-details', function () {
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
            success: function (messages) {
                messages.forEach(function (message) {
                    var messageText = '';
                    if (message.is_user === 1) {
                        // If the user is 1, use gray color card
                        messageText += '<div class="card text-white bg-primary user-message" style="float: right; clear: both; background-color: #ccc; margin-bottom: 10px;">';
                    } else {
                        // If the user is 0, use blue color card
                        messageText += '<div class="card text-white bg-secondary other-message" style="float: left; clear: both; background-color: #007bff; color: #fff; margin-bottom: 10px;">';
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

    $('#closeTicket').click(function () {
        var ticketId = $('#ticket_id').val();

        $.ajax({
            url: '/tickets/' + ticketId + '/close',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function (response) {
                alert('Ticket closed successfully');
                location.reload();
            },
            error: function (error) {
                alert('An error occurred while closing the ticket');
            }
        });
    });

    $('.auto-close').delay(5000).fadeOut('slow');

    $('#closeModalButton').click(function () {
        $('#ticketDetailsModal').modal('hide');
    });
});