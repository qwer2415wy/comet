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
        $("#data-card-content").load('../inc/php/dep/pages/filter.php?load=load', function () {
            console.log('load performed');
        });
    }
});

$(document.body).on('keyup', 'input', function () {
    if (this.hasAttribute('word') || this.hasAttribute('replacement') || this.hasAttribute('server')) {
        this.value = xssFilters.inHTMLData(this.value);

        if (this.hasAttribute('word')) {
            let attr = this.getAttribute('word');
            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3');
            $.get("../inc/php/dep/pages/filter.php?load=update&id=" + attr + "&word=" + value, function (data) {
                console.log(data)
            });
        } else if (this.hasAttribute('replacement')) {
            let attr = this.getAttribute('replacement');
            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3');
            value = value.length === 0 ? 'NULL' : value;
            $.get("../inc/php/dep/pages/filter.php?load=update&id=" + attr + "&replacement=" + value, function (data) {
                console.log(data)
            });
        } else if (this.hasAttribute('server')) {
            let attr = this.getAttribute('server');
            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3');
            value = value.length === 0 ? 'NULL' : value;
            $.get("../inc/php/dep/pages/filter.php?load=update&id=" + attr + "&server=" + value, function (data) {
                console.log(data)
            });
        }
    }
});

$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('word')) {
        let id = this.getAttribute('word');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this filter word!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/filter.php?load=remove&id=" + id, function (data) {
                console.log('deleted ' + id);
                $("#data-card-content").load('../inc/php/dep/pages/filter.php?load=load', function () {
                    console.log('loaded');
                });
            });
        });
    }
});

$("form").submit(function (event) {
    if (this.id === 'filter') {
        event.preventDefault();
        var string = document.getElementById('search-input').value;
        string = xssFilters.inHTMLData(string);
        $.get("../inc/php/dep/pages/filter.php?load=add&string=" + string, function (data) {
            console.log('added ' + string);
            $("#data-card-content").load('../inc/php/dep/pages/filter.php?load=load', function () {
                console.log('loaded');
            });
        });
        document.getElementById('search-input').value = '';
    }
});

var isFilterLoaded = true;