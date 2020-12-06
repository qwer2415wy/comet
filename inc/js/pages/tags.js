/* ---------------- PAGES ------------------ */
$(document).on('click','a',function() {
    if (this.hasAttribute('nmtags')) {
        let attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-card-content").load('../inc/php/dep/pages/tags.php?p=' + attr, function () {
                console.log('tags load performed');
            });
        }
    }
});

$(document.body).on('click', 'button' ,function() {
    if (this.hasAttribute('nmedit-tag')) {
        let id = this.getAttribute('nmedit-tag');
        $("#cached-modal").load("../inc/php/dep/pages/tags.php?load=load_tag&id=" + id, function () {
            let x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-tag') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-tag')) {
        let id = this.getAttribute('nmdelete-tag');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this tag!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/tags.php?load=delete_tag&id=" + id, function (data) {
                $("#data-card-content").load('../inc/php/dep/pages/tags.php?p=1', function () {
                    console.log('done delete tag');
                });
            });
        });
    } else if(this.hasAttribute('modal')) {
        let x = document.getElementsByClassName("modal");
        for(let i = 0; i < x.length; i++) {
            if(x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if(this.hasAttribute('close')) {
        let x = document.getElementsByClassName("modal");
        for(let i = 0; i < x.length; i++) {
            if(x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('submit', 'form' ,function(e) {
    e.preventDefault();
    if (this.id === 'create-tag-form') {
        let data = $(this).serialize();
        $.get("../inc/php/dep/pages/tags.php?load=create_tag&" + data, function (data) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/tags.php?p=1', function () {
                console.log('done create tag');
            });
        });
    } else if(this.id === 'nmedit-tag-form') {
        let tagId = this.getAttribute('tag');
        let data = $(this).serialize();
        $.get( "../inc/php/dep/pages/tags.php?load=edit_tag&id="  + tagId + "&" + data, function( data ) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/tags.php?p=1', function () {
                console.log('loaded');
                swal({
                    title: "Success",
                    text: "Successfully modified the tag " + data['name'] + " !\nExecute '/tags reload " + tagId + "' to apply the changes!",
                    type: "success",
                    closeOnConfirm: true
                });
            });
        });
    }
});

var isTagsLoaded = true;