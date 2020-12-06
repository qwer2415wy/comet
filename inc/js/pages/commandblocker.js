$("input#search-input").focus(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).addClass('is-editing');
    });
});

$("input#search-input").focusout(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).removeClass('is-editing');
    });
});

$("input#search-input").keyup(function () {
    if ($(this).val().length === 0) {
        console.log($(this).val().length);
        $("#data-card-content").load('../inc/php/dep/pages/commandblocker.php', function () {
            console.log('load performed');
        });
    }
});

$(document.body).on('keyup', 'input', function () {
    if (this.hasAttribute('command')) {
        let attr = this.getAttribute('command');
        let value = xssFilters.inHTMLData(this.value
            .replace(new RegExp('\\+', 'g'), ';v7'));
        $.get("../inc/php/dep/pages/commandblocker.php?load=update&id=" + attr + "&command=" + value, function (data) {
            console.log(data);
        });
    } else if (this.hasAttribute('server')) {
        let attr = this.getAttribute('server');
        let value = xssFilters.inHTMLData(this.value);
        $.get("../inc/php/dep/pages/commandblocker.php?load=update&id=" + attr + "&server=" + value, function (data) {
            console.log('data: ' + data);
        });
    }
});

$(document.body).on('click', '.switch-input', function () {
    if (this.hasAttribute('nmsetcommandbypasspermission')) {
        console.log(this.id + " has been " + this.checked);
        $.get("../inc/php/dep/pages/commandblocker.php?load=update&id=" + this.id + "&bypass=" + this.checked, function (data) {
            console.log(data);
        });
    }
});

$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('command')) {
        let id = this.getAttribute('command');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this commandblocker command!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/commandblocker.php?load=remove&id=" + id, function (data) {
                console.log('deleted');
                $("#data-card-content").load('../inc/php/dep/pages/commandblocker.php', function () {
                    console.log('loaded');
                });
            });
        });
    }
});

$("form").submit(function (event) {
    if (this.id === 'commandblocker') {
        event.preventDefault();
        let string = document.getElementById('search-input').value;
        string = xssFilters.inHTMLData(string
            .replace(new RegExp('\\+', 'g'), ';v7'));
        $.get("../inc/php/dep/pages/commandblocker.php?load=add&string=" + string, function (data) {
            console.log(data);
            $("#data-card-content").load('../inc/php/dep/pages/commandblocker.php', function () {
                console.log('loaded');
            });
        });
        document.getElementById('search-input').value = '';
    }
});

var isCommandBlockerLoaded = true;