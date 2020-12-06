$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('nmdelete-announcement')) {
        let id = this.getAttribute('nmdelete-announcement');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this announcement!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/announcementsmanager.php?load=delete_announcement&id=" + id, function (data) {
                $("#data-card-content").load('../inc/php/dep/pages/announcements.php', function () {
                    console.log('done delete group');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-announcement')) {
        let id = this.getAttribute('nmedit-announcement');
        $("#cached-modal").load("../inc/php/dep/pages/announcementsmanager.php?load=load_announcement&id=" + id, function () {
            let x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-announcement') {
                    x[i].style.display = "block"
                }
            }
        });
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
    if (this.hasAttribute('nmenableannouncementpermission')) {
        let finalId = this.id.replace('perm_', '');
        console.log(finalId + " has been " + this.checked);
        $.get('../inc/php/dep/pages/announcementsmanager.php?load=enableannouncementpermission&id=' + finalId + '&permission=' + this.checked, function (data) {
            console.log(data);
        });
    } else if (this.hasAttribute('nmsetannouncementactive')) {
        let finalId = this.id.replace('active_', '');
        console.log(finalId + " has been " + this.checked);
        $.get('../inc/php/dep/pages/announcementsmanager.php?load=setannouncementactive&id=' + finalId + '&active=' + this.checked, function (data) {
            console.log(data);
        });
    }
});

$(document.body).on('change', 'select', function (e) {
    e.preventDefault();
    if (this.id === 'announcement-type') {
        var optionSelected = $(this).find("option:selected");
        let txt = getAnnouncementType(optionSelected.val());
        console.log(txt);
        if (txt.endsWith('all servers')) {
            document.getElementById("server").style.display = "none";
            document.getElementById("serverinput").required = false;
        } else {
            document.getElementById("server").style.display = "block";
            document.getElementById("serverinput").required = true;
        }
        if (txt.startsWith('title')) {
            document.getElementById('messageinput').value = '{"title": "Title here", "subtitle": "Subtitle here"}';
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-announcement-form') {
        let data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/announcementsmanager.php?load=add_announcement&" + data, function (data) {
            if (data === 'true') {
                console.log('Successfully added announcement');
            }
            $("#data-card-content").load('../inc/php/dep/pages/announcements.php', function () {
                console.log('loaded');
            });
        });
    } else if (this.id === 'nmedit-announcement-form') {
        let data = $(this).serialize();
        console.log(data);
        console.log(this.getAttribute('announcement'));
        $.get("../inc/php/dep/pages/announcementsmanager.php?load=edit_announcement&id=" + this.getAttribute('announcement') + "&" + data, function (data) {
            console.log(data);
            $("#data-card-content").load('../inc/php/dep/pages/announcements.php', function () {
                console.log('loaded');
            });
        });
    }
});

function getAnnouncementType(type) {
    switch (type) {
        case '1':
            return 'chat all servers';
        case '2':
            return "chat servers only";
        case '3':
            return "chat servers except";
        case '4':
            return "actionbar all servers";
        case '5':
            return "actionbar servers only";
        case '6':
            return "actionbar servers except";
        case '7':
            return "title all servers";
        case '8':
            return "title servers only";
        case '9':
            return "title servers except";
    }
}

var isAnnouncementsLoaded = true;