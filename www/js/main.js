$(document).ready(function () {
    //form-competitor show
    $('.show-form-competitor').on('click', function () {
        if ($(this).data("id")) {
            $('#newCompetitorModalCentre-' + $(this).data("id")).modal('show');
        } else {
            $('#newCompetitorModalCentre').modal('show');
        }
    })

    //form-comp show
    $('.show-form-comp').on('click', function () {
        if ($(this).data("id")) {
            $('#newCompModalCentre-' + $(this).data("id")).modal('show');
        } else {
            $('#newCompModalCentre').modal('show');
        }
    })

    //form-category-form
    $('.show-form-category').on('click', function () {
        console.log($(this).data("id-category"));
        if ($(this).data("id-category") !== undefined) {
            $('#newCategoryModalCentre-' + $(this).data("id") + '-' + $(this).data("id-category") ).modal('show');
        } else {
            $('#newCategoryModalCentre-' + $(this).data("id")).modal('show');
        }
    });

    //show category
    $('.show-category').on('click', function () {
        $('#category-list-' + $(this).data("id")).toggle('slow');
    });

    //timeout for flashMessage
    setTimeout(function() {
        $('#myAlert').hide('slow');
    }, 2000);
});




