$(document.body).on('keyup', 'textarea', function () {
/*    var options = {
        whiteList: {
            script: ["<", ">"]
        }
    };
    this.value = filterXSS(this.value, options);*/
    console.log(this.value);
    let id = 0;
    if (this.hasAttribute('mid')) {
        id = this.getAttribute('mid');
    }
    switch (this.id) {
        case'message': {
            let yourMOTD = this.value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
            let newMOTD = yourMOTD.replaceColorCodes();
            $('#motd').text('').append(newMOTD).html();

            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3')
                .replace(new RegExp('#', 'g'), ';v4')
                .replace(new RegExp('<', 'g'), ';v5')
                .replace(new RegExp('>', 'g'), ';v6')
                .replace(new RegExp('\\+', 'g'), ';v7')
            ;
            $.get("../inc/php/pages/motd.php?load=set_motd_text&id=" + id + "&text=" + value, function (data) {
                console.log("data: " + data)
            });
            break;
        }
        case'description': {
            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3')
                .replace(new RegExp('#', 'g'), ';v4')
                .replace(new RegExp('<', 'g'), ';v5')
                .replace(new RegExp('>', 'g'), ';v6')
                .replace(new RegExp('\\+', 'g'), ';v7')
            ;
            $.get("../inc/php/pages/motd.php?load=set_motd_description&id=" + id + "&description=" + value, function (data) {
                console.log(data)
            });
            break;
        }
        case 'maintenance_message': {
            let yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
            let newMOTD = yourMOTD.replaceColorCodes();
            $('#motd_maintenance').text('').append(newMOTD).html();
            if (this.hasAttribute('variable')) {
                let attr = this.getAttribute('variable');
                let value = this.value
                    .replace(new RegExp(' ', 'g'), ';v1')
                    .replace(new RegExp('&', 'g'), ';v2')
                    .replace(new RegExp('§', 'g'), ';v3')
                    .replace(new RegExp('#', 'g'), ';v4')
                    .replace(new RegExp('<', 'g'), ';v5')
                    .replace(new RegExp('>', 'g'), ';v6')
                    .replace(new RegExp('\\+', 'g'), ';v7')
                ;
                $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function (data) {
                    console.log(data)
                });
            }
            break;
        }
        case 'maintenance_description': {
            if (this.hasAttribute('variable')) {
                let attr = this.getAttribute('variable');
                console.log(attr);
                let value = this.value
                    .replace(new RegExp(' ', 'g'), ';v1')
                    .replace(new RegExp('&', 'g'), ';v2')
                    .replace(new RegExp('§', 'g'), ';v3')
                    .replace(new RegExp('#', 'g'), ';v4')
                    .replace(new RegExp('<', 'g'), ';v5')
                    .replace(new RegExp('>', 'g'), ';v6')
                    .replace(new RegExp('\\+', 'g'), ';v7')
                ;
                $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function (data) {
                    console.log(data)
                });
            }
            break;
        }
    }
});

$(document.body).on('keyup', 'input', function () {
    this.value = xssFilters.inHTMLData(this.value);
    //console.log(this.value);
    //console.log(this.id);
    let id = 0;
    if (this.hasAttribute('mid')) {
        id = this.getAttribute('mid');
    }
    switch (this.id) {
        case 'icon':
            let url = this.value;
            if (url.endsWith('.png') || url.endsWith('.jpg')) {
                console.log('proceed please');
                $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                $.get("../inc/php/pages/motd.php?load=set_motd_faviconurl&id=" + id + "&url=" + url, function (data) {
                    console.log(data)
                });
            }
            break;
        case 'customversion': {
            let version = this.value;
            let newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
            $('#ping').text('').append(newVersion).html();

            let value = this.value
                .replace(new RegExp(' ', 'g'), ';v1')
                .replace(new RegExp('&', 'g'), ';v2')
                .replace(new RegExp('§', 'g'), ';v3')
                .replace(new RegExp('#', 'g'), ';v4')
                .replace(new RegExp('<', 'g'), ';v5')
                .replace(new RegExp('>', 'g'), ';v6');
            $.get("../inc/php/pages/motd.php?load=set_motd_customversion&id=" + id + "&customversion=" + value, function (data) {
                console.log(data)
            });
            break;
        }
        default:
            if (this.hasAttribute('variable')) {
                let attr = this.getAttribute('variable');
                if (attr === 'maintenance_customversion') {
                    let yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
                    let newMOTD = yourMOTD.replaceColorCodes();
                    $('#maintenance_ping').text('').append(newMOTD).html();
                }
                let value = this.value
                    .replace(new RegExp(' ', 'g'), ';v1')
                    .replace(new RegExp('&', 'g'), ';v2')
                    .replace(new RegExp('§', 'g'), ';v3')
                    .replace(new RegExp('#', 'g'), ';v4')
                    .replace(new RegExp('<', 'g'), ';v5')
                    .replace(new RegExp('>', 'g'), ';v6')
                    .replace(new RegExp('\\+', 'g'), ';v7')
                ;
                $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function (data) {
                    console.log(data)
                });
            }
            break;
    }
    /*if (this.id === 'icon') {
        let url = this.value;
        if (url.endsWith('.png') || url.endsWith('.jpg')) {
            console.log('proceed please');
            $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
        }
    } else if (this.id === 'message') {
        let yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
        let newMOTD = yourMOTD.replaceColorCodes();
        $('#motd').text('').append(newMOTD).html();
    } else if (this.id === 'maintenance_message') {
        let yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
        let newMOTD = yourMOTD.replaceColorCodes();
        $('#motd_maintenance').text('').append(newMOTD).html();
    } else if (this.hasAttribute('variable')) {
        let attr = this.getAttribute('variable');
        let value = this.value
            .replace(new RegExp(' ', 'g'), ';v1')
            .replace(new RegExp('&', 'g'), ';v2')
            .replace(new RegExp('§', 'g'), ';v3');
        $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function (data) {
            console.log(data)
        });
    }*/
});

$(document.body).on('change', 'select', function (e) {
    e.preventDefault();
    if (this.id === 'motdIds') {
        var optionSelected = $(this).find("option:selected");
        let id = optionSelected.val();
        console.log(id);
        $("#motd_zone").load("../inc/php/pages/motd.php?load=load_motd&id=" + id, function () {
            let yourMOTD = document.getElementById('message').value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
            let newMOTD = yourMOTD.replaceColorCodes();
            $('#motd').text('').append(newMOTD).html();

            let version = document.getElementById('customversion').value;
            let newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
            $('#ping').text('').append(newVersion).html();

            let url = document.getElementById('icon').value;
            if (url.endsWith('.png') || url.endsWith('.jpg')) {
                console.log('proceed please');
                $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                $('#preview_zone_maintenance').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
            }
        });
    }
});

$(document.body).on('click', 'button', function () {
    if (this.id === 'create-motd') {
        $.get('../inc/php/pages/motd.php?load=create_motd', function (id) {
            $('#motd_select_zone').load('../inc/php/pages/motd.php?load=reload_motd_dropdown&id=' + id, function (data) {
                $("#motd_zone").load("../inc/php/pages/motd.php?load=load_motd&id=" + id, function () {
                    let yourMOTD = document.getElementById('message').value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
                    let newMOTD = yourMOTD.replaceColorCodes();
                    $('#motd').text('').append(newMOTD).html();

                    let version = document.getElementById('customversion').value;
                    let newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
                    $('#ping').text('').append(newVersion).html();

                    let url = document.getElementById('icon').value;
                    if (url.endsWith('.png') || url.endsWith('.jpg')) {
                        console.log('proceed please');
                        $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                        $('#preview_zone_maintenance').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                    }
                });
            })
        });
    } else if (this.id === 'delete-motd') {
        if (this.hasAttribute("mid")) {
            let id = this.getAttribute("mid");
            $.get('../inc/php/pages/motd.php?load=delete_motd&id=' + id, function () {
                $('#motd_select_zone').load('../inc/php/pages/motd.php?load=reload_motd_dropdown&id=0', function (data) {
                    $("#motd_zone").load("../inc/php/pages/motd.php?load=load_motd&id=0", function () {
                        let yourMOTD = document.getElementById('message').value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
                        let newMOTD = yourMOTD.replaceColorCodes();
                        $('#motd').text('').append(newMOTD).html();

                        let version = document.getElementById('customversion').value;
                        let newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
                        $('#ping').text('').append(newVersion).html();

                        let url = document.getElementById('icon').value;
                        if (url.endsWith('.png') || url.endsWith('.jpg')) {
                            console.log('proceed please');
                            $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                            $('#preview_zone_maintenance').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                        }
                    });
                })
            });
        }
    }
});

var isMotdLoaded = true;