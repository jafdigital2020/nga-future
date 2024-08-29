$(function () { // Document ready shorthand

    // Event handler for the login button
    $("#do_login").on('click', function (e) {
        e.preventDefault(); // Prevent form submission if not handled

        closeLoginInfo();

        var $parent = $(this).closest('.fieldset-body');
        var $spans = $parent.find('.validation');

        // Hide all status indicators and remove classes
        $spans.css("display", "none").removeClass("i-save i-warning i-close");

        var proceed = true;

        // Check each input field for non-empty values
        $("#login_form input[type='text'], #login_form input[type='password']").each(function () {
            var $input = $(this);
            var $span = $input.siblings('.validation');

            if (!$.trim($input.val())) {
                $span.addClass("i-warning").css("display", "block");
                proceed = false;
            }
        });

        if (proceed) {
            $spans.addClass("i-save").css("display", "block");
        }
    });

    // Reset previously shown results and hide messages on input keyup
    $("#login_form input").on('keyup', function () {
        $(this).siblings('.validation').css("display", "none");
    });

    // Initialize UI state
    openLoginInfo();
    setTimeout(closeLoginInfo, 1000);

    // Handle window resize
    $(window).on('resize', function () {
        closeLoginInfo();
    });
});

function openLoginInfo() {
    $('.b-form').css("opacity", "0.01");
    $('.box-form').css("left", "-37%");
    $('.box-info').css("right", "-37%");
}

function closeLoginInfo() {
    $('.b-form').css("opacity", "1");
    $('.box-form').css("left", "0px");
    $('.box-info').css("right", "-5px");
}
