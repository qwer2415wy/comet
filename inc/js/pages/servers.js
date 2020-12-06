$(document.body).on('click', 'a', function (e) {
    if (this.hasAttribute('nmbackservergroups')) {
        $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=1', function () {
            console.log('loaded');
        });
    }
});

/* ---------------- PAGES ------------------ */
$(document).on('click', 'a', function () {
    if (this.hasAttribute('nmservers')) {
        let attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-card-content").load('../inc/php/dep/pages/servers.php&p=' + attr, function () {
                console.log('group load performed');
            });
        }
    } else if (this.hasAttribute('nmservergroups')) {
        let attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=' + attr, function () {
                console.log('users load performed');
            });
        }
    }
});

$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('nmdelete-server')) {
        let id = this.getAttribute('nmdelete-server');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this server!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/servers.php?load=delete_server&id=" + id, function () {
                $("#data-card-content").load('../inc/php/dep/pages/servers.php?p=1', function () {
                    console.log('done delete server');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-server')) {
        let id = this.getAttribute('nmedit-server');
        $("#cached-modal").load("../inc/php/dep/pages/servers.php?load=load_server&id=" + id, function () {
            let x = document.getElementsByClassName("modal");
            for (let i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-server') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-servergroup')) {
        let id = this.getAttribute('nmdelete-servergroup');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this servergroup!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/servers.php?load=delete_servergroup&id=" + id, function () {
                $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=1', function () {
                    console.log('done delete servergroup');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-servergroup')) {
        let id = this.getAttribute('nmedit-servergroup');
        $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groupservers&id=' + id + '&p=1', function () {
            console.log('groupservers load performed');
        });
    } /*else if (this.hasAttribute('nmedit-servergroup')) {
        let id = this.getAttribute('nmedit-servergroup');
        console.log(id);
        $("#cached-modal").load("../inc/php/dep/pages/servers.php?load=load_servergroup&id=" + id, function () {
            let x = document.getElementsByClassName("modal");
            for (let i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-servergroup') {
                    x[i].style.display = "block"
                }
            }
        });
    }*/ else if (this.hasAttribute('nmdelete-groupserver')) {
        let serverid = this.getAttribute('nmdelete-groupserver');
        let groupid = this.getAttribute('servergroup');
        swal({
            title: "Are you sure?",
            //text: "You will not be able to recover this servergroup!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/servers.php?load=delete_server_from_group&groupid=" + groupid + '&serverid=' + serverid, function () {
                $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groupservers&id=' + groupid + '&p=1', function () {
                    console.log('done delete groupserver');
                });
            });
        });
    } else if (this.hasAttribute('nmaddgroupserver')) {
        let id = this.getAttribute('nmaddgroupserver');
        $("#cached-modal").load('../inc/php/dep/pages/servers.php?load=load_add_server_to_group&id=' + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-server-group') {
                    x[i].style.display = "block"
                }
            }
        });
        this.setAttribute('cachedgroupid', this.getAttribute('nmaddgroupserver'));
    } else if (this.hasAttribute('modal')) {
        let x = document.getElementsByClassName("modal");
        for (let i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if (this.hasAttribute('close')) {
        let x = document.getElementsByClassName("modal");
        for (let i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

/* --------- CHECKBOX ---------------- */

$(document.body).on('click', '.switch-input', function () {
    if (this.hasAttribute('nmsetserverrestricted')) {
        console.log(this.id + " has been " + this.checked);
        if (this.checked) {
            $.get("../inc/php/dep/pages/servers.php?load=setrestricted&id=" + this.id + "&value=1", function (data) {
                console.log('saved');
            });
        } else {
            $.get("../inc/php/dep/pages/servers.php?load=setrestricted&id=" + this.id + "&value=0", function (data) {
                console.log('saved');
            });
        }
    } else if (this.hasAttribute('nmsetlobbyserver')) {
        console.log(this.getAttribute('nmsetnmserverslobbyserver') + " has been " + this.checked);
        if (this.checked) {
            $.get("../inc/php/dep/pages/servers.php?load=setlobby&id=" + this.getAttribute('nmsetlobbyserver') + "&value=1", function (data) {
                console.log('saved');
            });
        } else {
            $.get("../inc/php/dep/pages/servers.php?load=setlobby&id=" + this.getAttribute('nmsetlobbyserver') + "&value=0", function (data) {
                console.log('saved');
            });
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-server-form') {
        let data = $(this).serialize();
        let servername = $('#servername').val();
        $.get("../inc/php/dep/pages/servers.php?load=add_server&" + data, function (response) {
            if (response === 'false') {
                swal({
                    title: "Error",
                    text: "Failed to insert server into database!\nPerhaps a server with that name already exists?",
                    type: "warning",
                    closeOnConfirm: true
                });
            } else if (response.toString().indexOf('Error: ') >= 0) {
                swal({
                    title: "Error",
                    text: "Failed to insert server into database!\n" + response.toString().replace('Error: '),
                    type: "warning",
                    closeOnConfirm: true
                });
            } else if (response === 'true') {
                swal({
                    title: "Success",
                    text: "Successfully added your new server!\nExecute '/servermanager reload " + servername + "' to register it.",
                    type: "success",
                    closeOnConfirm: true
                });
            }
            $("#data-card-content").load('../inc/php/dep/pages/servers.php?p=1', function () {
                console.log('loaded');
            });
        });
    } else if (this.id === 'create-servergroup-form') {
        let data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/servers.php?load=create_group&" + data, function (data) {
            if (data === 'false') {
                swal({
                    title: "Error",
                    text: "Failed to insert servergroup into database!\nPerhaps a servergroup with that name already exists?",
                    type: "warning",
                    closeOnConfirm: true
                });
            }
            $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=1', function () {
                console.log('done create servergroup');
            });
        });
    } else if (this.id === 'add-server-group-form') {
        let data = $(this).serialize();
        let id = this.getAttribute('group');
        $.get("../inc/php/dep/pages/servers.php?load=add_groupserver&groupid=" + id + "&" + data, function (data) {
            console.log(data);
            if (data === 'false') {
                swal({
                    title: "Error",
                    text: "Failed to insert server into database!\nPerhaps a server with that name already exists?",
                    type: "warning",
                    closeOnConfirm: true
                });
            }
            $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groupservers&id=' + id + '&p=1', function () {
                console.log('done create servergroup');
            });
        });
    } else if (this.id === 'nmedit-server-form') {
        let data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/servers.php?load=edit_server&id=" + this.getAttribute('server') + "&" + data, function (data) {
            console.log(data);
            $("#data-card-content").load('../inc/php/dep/pages/servers.php?p=1', function () {
                console.log('loaded');
            });
        });
    }
});

var isServersLoaded = true;