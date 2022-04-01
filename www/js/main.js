$(document).ready(function () {
    //potvrzovací dialog

    $(document).on('click', '.confirm-dialog', function (event) {
        let question = $(this).data.text;
        question = question ? question : "Opravdu si přejete provest tuto akci?";
        if (!window.confirm(question)) {
            event.preventDefault();
        }
    })

    naja.uiHandler.addEventListener('interaction', (event) => {
        const {element} = event.detail;
        const question = "Opravdu si přejete provest tuto akci?";
        if (question && ! window.confirm(question)) {
            event.preventDefault();
        }
    });


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

    //addFlashmessage
});

function addFlashmessage(message, type) {
    $("#flash-message").append(
        '<div id="myAlert" class="temporary"> ' +
        '<div id="myAlert" class="alert alert-' +  type + ' flash-message" role="alert">' +
        message +
        '</div></div>');
    setTimeout(function () {
        $('.temporary').hide('slow', function(){ $(this).remove(); });
    }, 4000);
}



function saveResult(dataTable, link) {
    $.ajax({
        type: 'POST',
        url:  link,
    data: dataTable,
        success: function () {
        addFlashmessage('Výsledky byly úspěšně uloženy!', 'success');
    },
    error: function () {
        addFlashmessage('Výsledky se nepovedlo uložit!', 'danger');
    }
});
}




