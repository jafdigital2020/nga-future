$(document).ready(function () {
    $("#example2").DataTable({
        order: [[0, "desc"]],
    });
});

$(window).scroll(function () {
    var scrollTop = $(window).scrollTop();
    var sidebar = $("#sidebar"); // update selector based on your HTML structure
    var offsetTop = sidebar.offset().top;

    if (scrollTop > offsetTop) {
        sidebar.addClass("sticky");
    } else {
        sidebar.removeClass("sticky");
    }
});
