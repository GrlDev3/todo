$(document).ready(function() {
    function loadTasks() {
        $.ajax({
            url: '../api/taskmanager.php',
            method: 'GET',
            success: function(tasks) {
                $('#task-list').empty();
                tasks = JSON.parse(tasks);
                tasks.forEach(function(task) {
                    $('#task-list').append('<div>' +
                         task.title +
                          '<button class="delete" data-id="' +
                           task.id +
                            '">Delete</button>' +
                            //' <button class="update" data-id="' +
                            // task.id + '">Update</button>' +
                    '</div>');
                });
            }
        });
    }

    loadTasks();

    $('#task-form').on('submit', function(e) {
        e.preventDefault();
        const title = $('#task-title').val();
        $.ajax({
            url: '../api/taskmanager.php',
            method: 'POST',
            data: { title: title },
            data: JSON.stringify({title:title}),
            success: function() {
                $('#task-title').val("");
                loadTasks();
            }
        });
    });

    /*$(document).on('click', '.update', function(){
        
    });*/

    $(document).on('click', '.delete', function() {
        const taskId = $(this).data('id');
        $.ajax({
            url: '../api/taskmanager.php?id=' + taskId,
            method: 'DELETE',
            success: function() {
                loadTasks();
            }
        });
    });
    
});
