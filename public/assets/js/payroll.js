$(document).ready(function () {
    // Function to fetch data
    function fetchData() {
        // Clear the input fields before making the AJAX request
        $('#hourlyRate').val('');
        $('#position').val('');
        $('#totalLate').val('00:00:00');
        $('#totalHours').val('00:00:00');
        $('#results').empty();

        // Serialize the form data
        var formData = $('#searchForm').serialize();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var userId = $('#user_id').val();

        // Check if user_id is selected
        if (userId) {
            $.ajax({
                url: "{{ route('emppayroll') }}",
                method: 'GET',
                data: formData,
                success: function (response) {
                    // Check if response contains the necessary data
                    if (response) {
                        // Set the value of the hourlyRate and position input fields
                        $('#hourlyRate').val(response.hourlyRate || '');
                        $('#position').val(response.position || '');

                        // Set the totalLate and totalHours fields only if they exist in the response
                        if (response.totalLate) {
                            $('#totalLate').val(response.totalLate);
                            // Compute late deduction
                            var hourlyRate = parseFloat(response.hourlyRate) || 0;
                            var totalLateMinutes = timeStringToMinutes(response.totalLate);
                            var lateDeduction = (hourlyRate / 60) * totalLateMinutes;
                            $('#late').val(lateDeduction.toFixed(2));
                        }
                        if (response.total) {
                            $('#totalHours').val(response.total);
                        }
                        // Salary Computation
                        var hourlyRate = parseFloat(response.hourlyRate) || 0;
                        var grossMonthly = hourlyRate * 8 * 22;
                        var grossBasic = grossMonthly / 2;
                        $('#grossMonthly').val(grossMonthly.toFixed(2));
                        $('#grossBasic').val(grossBasic.toFixed(2));

                        // Calculate and display contributions based on selected options
                        var sssContribution = 0;
                        var philHealthContribution = 0;
                        var pagIbigContribution = 0;
                        var withHolding = 0;
                        var birthdayPTO = 0;
                        var loan = parseFloat($('#loan').val()) || 0;
                        var advance = parseFloat($('#advance').val()) || 0;
                        var others = parseFloat($('#others').val()) || 0;

                        // Check if SSS option is selected
                        if ($('input[name="option_sss"]:checked').val() === 'yes') {
                            sssContribution = grossMonthly * 0.0225;
                            $('#sss').val(sssContribution.toFixed(2));
                        } else {
                            $('#sss').prop('readonly', true).val('');
                        }

                        // Check if PhilHealth option is selected
                        if ($('input[name="option_philhealth"]:checked').val() === 'yes') {
                            philHealthContribution = grossMonthly * 0.0125;
                            $('#philHealth').val(philHealthContribution.toFixed(2));
                        } else {
                            $('#philHealth').prop('readonly', true).val('');
                        }

                        // Check if Pag-Ibig option is selected
                        if ($('input[name="option_pagibig"]:checked').val() === 'yes') {
                            pagIbigContribution = 100.00;
                            $('#pagIbig').val(pagIbigContribution.toFixed(2));
                        } else {
                            $('#pagIbig').prop('readonly', true).val('');
                        }

                        // Check if withholding option is selected
                        if ($('input[name="option_tax"]:checked').val() === 'yes') {
                            withHolding = parseFloat($('#withHolding').val()) || 0;
                            $('#withHolding').prop('readonly', false);
                        } else {
                            $('#withHolding').prop('readonly', true).val('');
                        }

                        // Check if BirthdayPTO option is selected
                        if ($('input[name="option_birthday"]:checked').val() === 'yes') {
                            birthdayPTO = hourlyRate * 8;
                            $('#birthdayPTO').val(birthdayPTO.toFixed(2));
                        } else {
                            $('#birthdayPTO').prop('readonly', true).val('');
                        }

                        // Calculate total deduction
                        var totalDeduction = sssContribution + philHealthContribution +
                            pagIbigContribution + withHolding + loan + advance + others +
                            lateDeduction;
                        $('#totalDeduction').val(totalDeduction.toFixed(2));

                        // Display the fetched data in the results div
                        $.each(response.filteredData, function (index, data) {
                            $('#results').append('<div>' +
                                '<p>Name: ' + data.name + '</p>' +
                                '<p>Hourly Rate: ' + data.hourlyRate + '</p>' +
                                '<p>Position: ' + data.position + '</p>' +
                                '<p>Total Hours: ' + (response.total ||
                                    '00:00:00') + '</p>' +
                                '<p>Total Late: ' + (response.totalLate ||
                                    '00:00:00') + '</p>' +
                                '</div>');
                        });
                    } else {
                        $('#results').append('<p>No results found</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        } else {
            alert('Please select a user.');
            $('#user_id').val(''); // Reset the user selection
        }
    }

    // Function to convert time string (HH:mm:ss) to total minutes
    function timeStringToMinutes(timeString) {
        var parts = timeString.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    // Function to compute vacation leave value
    function computeSickVacationLeave() {
        var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;
        var vLeave = parseInt($('#vLeave').val()) || 0;
        var sLeave = parseInt($('#sLeave').val()) || 0;
        var otHours = parseInt($('#otHours').val()) || 0; // Updated otHours value
        var otCompute = hourlyRate * 0.25;
        var otTotal = (hourlyRate + otCompute) * otHours; // Compute otTotal dynamically
        var vacLeaveValue = hourlyRate * 8 * vLeave;
        var sickLeaveValue = hourlyRate * 8 * sLeave;
        $('#vacLeave').val(vacLeaveValue.toFixed(2));
        $('#sickLeave').val(sickLeaveValue.toFixed(2));
        $('#otTotal').val(otTotal.toFixed(2)); // Update otTotal value
    }

    // Update vacation leave value when vLeave select changes
    $('#vLeave, #sLeave, #otHours').on('change', function () {
        computeSickVacationLeave();
    });

    // Reset user selection when start_date is clicked
    $('#start_date, #end_date').on('click', function () {
        $('#user_id').val('');
        $('#position').val('');
        $('#totalHours').val('');
        $('#vLeave').val('');
        $('#vacLeave').val('');
        $('#sLeave').val('');
        $('#sickLeave').val('');
    });


    // Fetch data when start_date or end_date is changed
    $('#start_date, #end_date').on('change', function () {
        fetchData();
    });

    // Fetch data when user_id is changed
    $('#user_id').on('change', function () {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        $('#vLeave').val('0');
        $('#vacLeave').val('');
        $('#sLeave').val('0');
        $('#sickLeave').val('');
        if (startDate && endDate) {
            fetchData();
        } else {
            alert('Please select both start date and end date.');
            $('#user_id').val(''); // Reset the user selection
        }
    });

    // Optional: Fetch data when the form is submitted (if you have a submit button)
    $('#searchForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission
        fetchData();
    });

    // SSS BUTTON FUNCTION
    $('input[name="option_sss"]').on('change', function () {
        if ($(this).val() === 'yes') {
            // If "Yes" is selected, calculate and set SSS contribution
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var sssContribution = grossMonthly * 0.0225;
            $('#sss').val(sssContribution.toFixed(2));
            $('#sss').prop('readonly', false);
        } else {
            // If "No" is selected, make the input readonly and clear its value
            $('#sss').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // PHILHEALTH BUTTON FUNCTION
    $('input[name="option_philhealth"]').on('change', function () {
        if ($(this).val() === 'yes') {
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var philHealthContribution = grossMonthly * 0.0125;
            $('#philHealth').val(philHealthContribution.toFixed(2));
            $('#philHealth').prop('readonly', false);
        } else {
            $('#philHealth').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // PagIbig BUTTON FUNCTION
    $('input[name="option_pagibig"]').on('change', function () {
        if ($(this).val() === 'yes') {
            var pagIbigContribution = 100.00;
            $('#pagIbig').val(pagIbigContribution.toFixed(2));
            $('#pagIbig').prop('readonly', false);
        } else {
            $('#pagIbig').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // withHolding BUTTON FUNCTION
    $('input[name="option_tax"]').on('change', function () {
        if ($(this).val() === 'yes') {
            $('#withHolding').prop('readonly', false);
        } else {
            $('#withHolding').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // Update total deduction when the withholding input field changes
    $('#withHolding, #loan, #advance, #others').on('input', function () {
        updateTotalDeduction();
    });

    // PHILHEALTH BUTTON FUNCTION
    $('input[name="option_philhealth"]').on('change', function () {
        if ($(this).val() === 'yes') {
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var philHealthContribution = grossMonthly * 0.0125;
            $('#philHealth').val(philHealthContribution.toFixed(2));
            $('#philHealth').prop('readonly', false);
        } else {
            $('#philHealth').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // PagIbig BUTTON FUNCTION
    $('input[name="option_pagibig"]').on('change', function () {
        if ($(this).val() === 'yes') {
            var pagIbigContribution = 100.00;
            $('#pagIbig').val(pagIbigContribution.toFixed(2));
            $('#pagIbig').prop('readonly', false);
        } else {
            $('#pagIbig').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // withHolding BUTTON FUNCTION
    $('input[name="option_tax"]').on('change', function () {
        if ($(this).val() === 'yes') {
            $('#withHolding').prop('readonly', false);
        } else {
            $('#withHolding').prop('readonly', true).val('');
        }
        updateTotalDeduction();
    });

    // BirthdayPTO BUTTON FUNCTION
    $('input[name="option_birthday"]').on('change', function () {
        var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;
        if ($(this).val() === 'yes') {
            var birthdayPTO = hourlyRate * 8;
            $('#birthdayPTO').val(birthdayPTO.toFixed(2));
            $('#birthdayPTO').prop('readonly', false);
        } else {
            $('#birthdayPTO').prop('readonly', true).val('');
        }
    });

    // Update total deduction when the withholding input field changes
    $('#withHolding, #loan, #advance, #others').on('input', function () {
        updateTotalDeduction();
    });

    function updateTotalDeduction() {
        var sssContribution = parseFloat($('#sss').val()) || 0;
        var philHealthContribution = parseFloat($('#philHealth').val()) || 0;
        var pagIbigContribution = parseFloat($('#pagIbig').val()) || 0;
        var withHolding = parseFloat($('#withHolding').val()) || 0;
        var loan = parseFloat($('#loan').val()) || 0;
        var advance = parseFloat($('#advance').val()) || 0;
        var others = parseFloat($('#others').val()) || 0;
        var lateDeduction = parseFloat($('#late').val()) || 0;

        var totalDeduction = sssContribution + philHealthContribution + pagIbigContribution + withHolding +
            loan + advance + others + lateDeduction;
        $('#totalDeduction').val(totalDeduction.toFixed(2));
    }
});



// $(document).ready(function () {
//     // Function to fetch data
//     function fetchData() {
//         // Clear the input fields before making the AJAX request
//         $('#hourlyRate').val('');
//         $('#position').val('');
//         $('#totalLate').val('00:00:00');
//         $('#totalHours').val('00:00:00');
//         $('#results').empty();

//         // Serialize the form data
//         var formData = $('#searchForm').serialize();
//         var startDate = $('#start_date').val();
//         var endDate = $('#end_date').val();
//         var userId = $('#user_id').val();

//         // Check if user_id is selected
//         if (userId) {
//             $.ajax({
//                 url: "{{ route('emppayroll') }}",
//                 method: 'GET',
//                 data: formData,
//                 success: function (response) {
//                     // Check if response contains the necessary data
//                     if (response) {
//                         // Set the value of the hourlyRate and position input fields
//                         $('#hourlyRate').val(response.hourlyRate || '');
//                         $('#position').val(response.position || '');

//                         // Set the totalLate and totalHours fields only if they exist in the response
//                         if (response.totalLate) {
//                             $('#totalLate').val(response.totalLate);
//                         }
//                         if (response.total) {
//                             $('#totalHours').val(response.total);
//                         }

//                         // Salary Computation
//                         var hourlyRate = parseFloat(response.hourlyRate) || 0;
//                         var grossMonthly = hourlyRate * 8 * 22;
//                         var grossBasic = grossMonthly / 2;
//                         $('#grossMonthly').val(grossMonthly.toFixed(2));
//                         $('#grossBasic').val(grossBasic.toFixed(2));

//                         // Calculate and display contributions based on selected options
//                         var sssContribution = 0;
//                         var philHealthContribution = 0;
//                         var pagIbigContribution = 0;
//                         var withHolding = 0;
//                         var loan = parseFloat($('#loan').val()) || 0;
//                         var advance = parseFloat($('#advance').val()) || 0;
//                         var others = parseFloat($('#others').val()) || 0;

//                         // Check if SSS option is selected
//                         if ($('input[name="option_sss"]:checked').val() === 'yes') {
//                             sssContribution = grossMonthly * 0.0225;
//                             $('#sss').val(sssContribution.toFixed(2));
//                         } else {
//                             $('#sss').prop('readonly', true).val('');
//                         }

//                         // Check if PhilHealth option is selected
//                         if ($('input[name="option_philhealth"]:checked').val() === 'yes') {
//                             philHealthContribution = grossMonthly * 0.0125;
//                             $('#philHealth').val(philHealthContribution.toFixed(2));
//                         } else {
//                             $('#philHealth').prop('readonly', true).val('');
//                         }

//                         // Check if Pag-Ibig option is selected
//                         if ($('input[name="option_pagibig"]:checked').val() === 'yes') {
//                             pagIbigContribution = 100.00;
//                             $('#pagIbig').val(pagIbigContribution.toFixed(2));
//                         } else {
//                             $('#pagIbig').prop('readonly', true).val('');
//                         }

//                         // Check if withholding option is selected
//                         if ($('input[name="option_tax"]:checked').val() === 'yes') {
//                             withHolding = parseFloat($('#withHolding').val()) || 0;
//                             $('#withHolding').prop('readonly', false);
//                         } else {
//                             $('#withHolding').prop('readonly', true).val('');
//                         }

//                         // Calculate total deduction
//                         var totalDeduction = sssContribution + philHealthContribution +
//                             pagIbigContribution + withHolding + loan + advance + others;
//                         $('#totalDeduction').val(totalDeduction.toFixed(2));

//                         // Display the fetched data in the results div
//                         $.each(response.filteredData, function (index, data) {
//                             $('#results').append('<div>' +
//                                 '<p>Name: ' + data.name + '</p>' +
//                                 '<p>Hourly Rate: ' + data.hourlyRate + '</p>' +
//                                 '<p>Position: ' + data.position + '</p>' +
//                                 '<p>Total Hours: ' + (response.total ||
//                                     '00:00:00') + '</p>' +
//                                 '<p>Total Late: ' + (response.totalLate ||
//                                     '00:00:00') + '</p>' +
//                                 '</div>');
//                         });
//                     } else {
//                         $('#results').append('<p>No results found</p>');
//                     }
//                 },
//                 error: function (xhr, status, error) {
//                     console.error('Error fetching data:', error);
//                 }
//             });
//         } else {
//             alert('Please select a user.');
//             $('#user_id').val(''); // Reset the user selection
//         }
//     }

//     // Reset user selection when start_date is clicked
//     $('#start_date, #end_date').on('click', function () {
//         $('#user_id').val('');
//         $('#position').val('');
//         $('#totalHours').val('');
//     });


//     // Fetch data when start_date or end_date is changed
//     $('#start_date, #end_date').on('change', function () {
//         fetchData();
//     });

//     // Fetch data when user_id is changed
//     $('#user_id').on('change', function () {
//         var startDate = $('#start_date').val();
//         var endDate = $('#end_date').val();
//         if (startDate && endDate) {
//             fetchData();
//         } else {
//             alert('Please select both start date and end date.');
//             $('#user_id').val(''); // Reset the user selection
//         }
//     });

//     // Optional: Fetch data when the form is submitted (if you have a submit button)
//     $('#searchForm').on('submit', function (e) {
//         e.preventDefault(); // Prevent the default form submission
//         fetchData();
//     });

//     // SSS BUTTON FUNCTION
//     $('input[name="option_sss"]').on('change', function () {
//         if ($(this).val() === 'yes') {
//             // If "Yes" is selected, calculate and set SSS contribution
//             var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
//             var sssContribution = grossMonthly * 0.0225;
//             $('#sss').val(sssContribution.toFixed(2));
//             $('#sss').prop('readonly', false);
//         } else {
//             // If "No" is selected, make the input readonly and clear its value
//             $('#sss').prop('readonly', true).val('');
//         }
//         updateTotalDeduction();
//     });

//     // PHILHEALTH BUTTON FUNCTION
//     $('input[name="option_philhealth"]').on('change', function () {
//         if ($(this).val() === 'yes') {
//             var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
//             var philHealthContribution = grossMonthly * 0.0125;
//             $('#philHealth').val(philHealthContribution.toFixed(2));
//             $('#philHealth').prop('readonly', false);
//         } else {
//             $('#philHealth').prop('readonly', true).val('');
//         }
//         updateTotalDeduction();
//     });

//     // PagIbig BUTTON FUNCTION
//     $('input[name="option_pagibig"]').on('change', function () {
//         if ($(this).val() === 'yes') {
//             var pagIbigContribution = 100.00;
//             $('#pagIbig').val(pagIbigContribution.toFixed(2));
//             $('#pagIbig').prop('readonly', false);
//         } else {
//             $('#pagIbig').prop('readonly', true).val('');
//         }
//         updateTotalDeduction();
//     });

//     // withHolding BUTTON FUNCTION
//     $('input[name="option_tax"]').on('change', function () {
//         if ($(this).val() === 'yes') {
//             $('#withHolding').prop('readonly', false);
//         } else {
//             $('#withHolding').prop('readonly', true).val('');
//         }
//         updateTotalDeduction();
//     });

//     // Update total deduction when the withholding input field changes
//     $('#withHolding, #loan, #advance, #others').on('input', function () {
//         updateTotalDeduction();
//     });

//     function updateTotalDeduction() {
//         var sssContribution = parseFloat($('#sss').val()) || 0;
//         var philHealthContribution = parseFloat($('#philHealth').val()) || 0;
//         var pagIbigContribution = parseFloat($('#pagIbig').val()) || 0;
//         var withHolding = parseFloat($('#withHolding').val()) || 0;
//         var loan = parseFloat($('#loan').val()) || 0;
//         var advance = parseFloat($('#advance').val()) || 0;
//         var others = parseFloat($('#others').val()) || 0;

//         var totalDeduction = sssContribution + philHealthContribution + pagIbigContribution + withHolding +
//             loan + advance + others;
//         $('#totalDeduction').val(totalDeduction.toFixed(2));
//     }
// });
