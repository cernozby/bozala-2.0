$(document).ready(function () {
    //form-competitor show
    $(document).on('click', '.show-form-competitor', function () {
        if ($(this).data("id")) {
            $('#newCompetitorModalCentre-' + $(this).data("id")).modal('show');
        } else {
            $('#newCompetitorModalCentre').modal('show');
        }
    })

    //form-comp show
    $(document).on('click', '.show-form-comp', function () {
        if ($(this).data("id")) {
            $('#newCompModalCentre-' + $(this).data("id")).modal('show');
        } else {
            $('#newCompModalCentre').modal('show');
        }
    })

    //form-category-form
    $(document).on('click', '.show-form-category', function () {
        if ($(this).data("id-category") !== undefined) {
            $('#newCategoryModalCentre-' + $(this).data("id") + '-' + $(this).data("id-category")).modal('show');
        } else {
            console.log($(this).data("id"));
            $('#newCategoryModalCentre-' + $(this).data("id")).modal('show');
        }
    });

    $(document).on('click', '.show-category', function () {
        $('#category-list-' + $(this).data("id")).toggle('slow');
    });

    $(document).on('click', '#changeAdminPart', function () {
        let adminPart = $('.admin-part');
        if(adminPart.attr('style')) {
            adminPart.removeAttr("style");
            $('.get-green').addClass("bg-success");
            $('.get-red').addClass("bg-danger");
        } else {
            $('.get-green').removeClass("bg-success");
            adminPart.css({"display": "none"});

        }
    });

});




