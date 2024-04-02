window.selectedIngredients = []
$(document).ready(function() {
    $('.select2').select2({
        placeholder: ''
    });
    $('#hideModal').on('click', function() {
        $('#modal').fadeOut();
    })
    $('.close').on('click', function(e) {
        e.preventDefault()
        let target = $(this).data('target')
        $(target).fadeOut(500);
    })
    $('.open').on('click', function(e) {
        e.preventDefault()
        let target = $(this).data('target')
        $(target).css({ 'visibility': 'visible', 'opacity': '1' }).fadeIn(500);
    })
    $("#file").change(function() {
        readURL(this, "#preview");
        $('#preview').fadeIn(100);
    });

    $('.owl-carousel').owlCarousel({
        margin: 5,
        nav: false,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 3
            }
        }
    })
    $('.carousel-item, .recipe').each((i, element) => {
        let image = $(element).data('image')
        if (image == "" || image == null) {} else {
            if (image.length > 0)
                $(element).css('background-image', `url("${image}")`)
        }
    });
    $('#filter').on('keyup', function() {
        let v = $(this).val()
        $.grep($('#ingredientsList > div.ingredient'), function(item) {
            let re = new RegExp(v, "i");
            let c = $(item).find('input:checked')[0] || false
            if ($(item).data("name").match(re) && c == false)
                $(item).fadeIn(100)
            else
                $(item).fadeOut(100)

        });
    })
    $('input[type=checkbox]').on('click', function() {
        let id = $(this).data('id')
        if ($(this).prop('checked')) {
            window.selectedIngredients.push(id)
            $('#selected').append($(`.ingredient[data-id=${id}]`))
            $('#ingredientsList').remove($(`.ingredient[data-id=${id}]`))

        } else {
            window.selectedIngredients.pop(id)
            $('#selected').remove($(`.ingredient[data-id=${id}]`))
            $('#ingredientsList').append($(`.ingredient[data-id=${id}]`))

        }
        console.log(window.selectedIngredients)
    })
});