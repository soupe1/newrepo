$( document ).ready(function() {
    $.post({
        data: {department_select: $('#dept-select').find(":selected").val()},
        url: 'php/db-submit.php'
    }).done(function(resp) {
        $('#display-parent').html(resp);        
    }).fail(function(resp) {
        $('#display-parent').html("Error Retrieving Items");
    });

    $('#dept-select').on('change', function(e) {
        var sel = $( this ).find(":selected").html();
        $('#dept-header').text(sel);

        var val = $( this ).find(":selected").val();
        
        $.post({
            data: {department_select: $( this ).find(":selected").val()},
            url: 'php/db-submit.php'
        }).done(function(resp) {
            $('#display-parent').html(resp);
        }).fail(function(resp) {
            $('#display-parent').html("Error Retrieving Items");
        });
    });

    $('#table-select').on('change', function() {
        var sel = $( this ).find(":selected").val().split("/")[0];
        var formName = '#' + sel + "-form";
        $('.form-group').children('div').css('display', 'none');
        if ($(formName).css('display') == 'none') {
            $(formName).css('display', 'block');
        }
    });

    $('#insert-form').on('submit', function(e) {
        e.preventDefault();
        $('#form-fail').css('display', 'none');
        $('#form-success').css('display', 'none');

        var data = new FormData(this);
        
        $.post({
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            url: 'php/db-submit.php'
        }).done(function() {
            $('#form-fail').css('display', 'none');
            $('#form-success').css('display', 'block');

            var sel = $('#table-select').find(":selected").val();
            $('#insert-form')[0].reset();
            $('#table-select').val(sel);

        }).fail(function() {
            $('#form-success').css('display', 'none');
            $('#form-fail').css('display', 'block');
        })
    });

    $('#display-parent').on('dragstart', '.item', function(e) {
        drag(e.originalEvent);
    });

    $('#display-parent').on('dragover', '#cart-drop-zone', function(e) {
        allowDrop(e.originalEvent);
    });

    $('#display-parent').on('drop', '#cart-drop-zone', function(e) {
        drop(e.originalEvent);
        
        $('#cart-drop-zone').html('<b>Item Added to Cart!</b>');
        setTimeout(() => {
            $('#cart-drop-zone').html('Drag Items Here to Add to Cart!');
        }, 2500);
    });

    $('#signout').on('click', function(e) {
        document.cookie = "logged=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=localhost";    
        document.cookie = "logged-name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=localhost";
        document.cookie = "logged-type=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=localhost";
        location.reload();
    })
});

function allowDrop(e) {
    e.preventDefault();
}
  
function drag(e) {
    e.dataTransfer.setData("item", $(e.target).attr('data-value'));
}

function drop(e) {
    e.preventDefault();
    var data = e.dataTransfer.getData("item");
    var cart = JSON.parse(localStorage.getItem('cart'));
    
    if (cart == null) {
        localStorage.setItem('cart', JSON.stringify([data]));
    } else {
        cart.push(data);
        localStorage.setItem('cart', JSON.stringify(cart));
    }
}