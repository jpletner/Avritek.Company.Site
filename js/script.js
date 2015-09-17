
//Begin modal login code
// set focus when modal is opened
$('#loginModal').on('shown.bs.modal', function () {
    $("#emailForm").focus();
});

// show the modal onload
$('#loginModal').modal({
    show: true
});

// everytime the button is pushed, open the modal, and trigger the shown.bs.modal event
$('#openBtn').click(function () {
    $('#loginModal').modal({
        show: true
    });
});
//End of modal login code

//Begin modal equipment code
// set focus when modal is opened
$('#loginModal').on('shown.bs.modal', function () {
    $("#emailForm").focus();
});

// show the modal onload

$('#loginModal').modal({
    show: true
});

// everytime the button is pushed, open the modal, and trigger the shown.bs.modal event
$('#openBtn').click(function () {
    $('#loginModal').modal({
        show: true
    });
});
//End of modal equipment code