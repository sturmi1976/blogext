

$(document).ready(function () {
    $('.delete-link').on('click', function (event) {
        event.preventDefault();

        // Hole die URL aus dem Link
        const href = $(this).data('href');

        // Setze die URL in den Bestätigungsbutton der Modalbox
        $('#confirmDelete').attr('href', href);

        // Öffne die Modalbox
        $('#confirmModal').modal('show');
    });

    setTimeout(function() { 
        $("div.alert-danger").fadeOut('slow');
    }, 3000);

});
