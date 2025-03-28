<?php
// Database Connection
$conn = new mysqli('localhost', 'root', '', '_sms');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle Insert Event Request
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $description = $_POST['description'];
    $url = $_POST['url'];

    $stmt = $conn->prepare("INSERT INTO t_time_table (name, start_time, end_time, description, url, class, section, editor_id) VALUES (?, ?, ?, ?, ?, 'class1', 'A', '1')");
    $stmt->bind_param("sssss", $name, $start, $end, $description, $url);
    $stmt->execute();
    echo "Event added successfully";
    exit;
}

// Fetch Events
$result = $conn->query("SELECT * FROM t_time_table");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['s_no'],
        'title' => $row['name'],
        'start' => $row['start_time'],
        'end' => $row['end_time'],
        'description' => $row['description'],
        'url' => $row['url'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar</title>

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" />
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#eventModal">Add New Event</button>
    <div id="calendar"></div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="form-group">
                        <label>Event Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="datetime-local" class="form-control" name="start" id="startDate" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="datetime-local" class="form-control" name="end" id="endDate" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Event URL</label>
                        <input type="url" class="form-control" name="url">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize FullCalendar
    $('#calendar').fullCalendar({
        events: <?php echo json_encode($events); ?>,
        selectable: true,
        selectHelper: true,
        editable: true,

        // On Date Click, Open Modal and Set Dates
        select: function(start, end) {
            $('#startDate').val(moment(start).format('YYYY-MM-DDTHH:mm'));
            $('#endDate').val(moment(end).format('YYYY-MM-DDTHH:mm'));
            $('#eventModal').modal('show');
        },

        // Show Event Details on Click
        eventClick: function(event) {
            alert('Event: ' + event.title + '\nDescription: ' + event.description);
        }
    });

    // Save Event
    $('#saveEventBtn').on('click', function() {
        var formData = $('#eventForm').serialize();
        $.post('c.php', formData, function(response) {
            alert(response);
            location.reload();
        });
    });
});
</script>
</body>
</html>
