$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('modal')) {
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
    } else if (this.hasAttribute('nmdelete-pre-punishment')) {
        var id = this.getAttribute('nmdelete-pre-punishment');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this punishment template!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/punishmentmanagement.php?type=delete_pre_punishment&id=" + id, function (data) {
               $("#data-card-content").load('../inc/php/dep/pages/prepunishments.php?p=1', function () {
					console.log('done delete punishment template');
				});
			});
		});
	} else if (this.hasAttribute('nmedit-pre-punishment')) {
        let id = this.getAttribute('nmedit-pre-punishment');
        $("#cached-modal").load("../inc/php/dep/pages/punishmentmanagement.php?type=load_pre_punishment&id=" + id, function (response) {
            let x = document.getElementsByClassName("modal");
            for (let i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-pre-punishment') {
                    x[i].style.display = "block"
                }
            }
        });
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-pre-punishment-form') {
        let data = $(this).serialize();
        $.get("../inc/php/dep/pages/punishmentmanagement.php?type=create_pre_punishment&" + data, function (response) {
            if (response === 'invalid duration') {
                swal({
                    title: "Error",
                    text: "Failed to insert punishment into database!\nInvalid Duration!",
                    type: "warning",
                    closeOnConfirm: true
                });
            } else if (response.startsWith('false')) {
                swal({
                    title: "Error",
                    text: "Failed to insert punishment into database!\n" + response.replace('false', ''),
                    type: "warning",
                    closeOnConfirm: true
                });
            }
            $("#data-card-content").load('../inc/php/dep/pages/prepunishments.php?p=1', function () {
                console.log('done create punishment');
            });
        });
    } else if (this.id === 'nmedit-pre-punishment-form') {
        let data = $(this).serialize();
        let id = this.getAttribute('pre-punishment');
        console.log("id=" + id + "&" + data);
        $.get("../inc/php/dep/pages/punishmentmanagement.php?type=edit_pre_punishment&id=" + id + "&" + data, function (response) {
            console.log(response);
            $("#data-card-content").load('../inc/php/dep/pages/prepunishments.php?p=1', function () {
                console.log('loaded');
            });
        });
    }
});

$(document.body).on('change', 'select', function (e) {
    e.preventDefault();
    if (this.id === 'prepunishment-type') {
        let optionSelected = $(this).find("option:selected");
        let txt = getPunishmentType(optionSelected.val());
        if (txt.substr(0, "temporary".length) === "temporary" || txt.substr(7, "temporary".length) === "temporary") {
            document.getElementById("duration").style.display = "block";
        } else {
            document.getElementById("duration").style.display = "none";
        }
        if (txt.substr(0, "global".length) === "global") {
            document.getElementById("server").style.display = "none";
            document.getElementById("serverinput").required = false;
        } else if (txt.substr(0, "warn".length) === "warn") {
            document.getElementById("server").style.display = "none";
            document.getElementById("serverinput").required = false;
        } else {
            document.getElementById("server").style.display = "block";
            document.getElementById("serverinput").required = true;
        }
    }
});

function getPunishmentType(type) {
    switch (type) {
        case '1':
            return 'ban';
        case '2':
            return 'global ban';
        case '3':
            return 'temporary ban';
        case '4':
            return 'global temporary ban';
        case '5':
            return 'ip ban';
        case '6':
            return 'global ip ban';
        case '7':
            return 'temporary ip ban';
        case '8':
            return 'global temporary ip ban';
        case '9':
            return 'mute';
        case '10':
            return 'global mute';
        case '11':
            return 'temporary mute';
        case '12':
            return 'global temporary mute';
        case '13':
            return 'ip mute';
        case '14':
            return 'global ip mute';
        case '15':
            return 'temporary ip mute';
        case '16':
            return 'global temporary ip mute';
        case '17':
            return 'kick';
        case '18':
            return 'global kick';
        case '19':
            return 'warn';
        case '20':
            return 'report';
        default:
            return 'unknown';
    }
}

var isPrePunishmentsLoaded = true;