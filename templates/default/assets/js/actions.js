window.lastSelected = null;
window.resultElement = null;
$(document).ready(function() {
    $('#Search').on('click', function() {
        let ingredients = window.selectedIngredients.sort()
        ingredients = ingredients.join(',')
        let mode = $('#strictMode').prop('checked')
        window.location.href = "?page=results&ingredients=" + ingredients + "&mode=" + (mode ? 'strict' : 'all')
    })
    $('.recipe-item:not(.nextPage)').on('click', function() {
        let id = $(this).data('id')
        Action('get-recipe', { id: id }, function(response) {
            let R = JSON.parse(response)
            let ings = JSON.parse(R.message.rt_recipe_ingredients_full)
            let Ings = '';
            let Extra = ""
            ings.forEach(i => {
                Extra = ""
                if (i.toLowerCase().includes("optional")) {
                    Extra = "optional"
                    i = `<div>${i}</div>`
                }
                Ings += `<div class="ingredient ${Extra}">${i}</div>`
            })
            let Instructions = R.message.rt_recipe_text.split(".")
            let Insts = '';
            var a = 1
            Instructions.forEach(i => {
                if (i.length > 0) {
                    Insts += `<div class="instruction"><span>${a}</span><div>${i}</div></div>`
                    a++
                }
            })
            $('#recipe-ingredients').html(Ings)
            $('#recipe-text').html(Insts)
            $('#recipe-prep').html(R.message.rt_recipe_prep)
            $('#recipe-duration').html(R.message.rt_recipe_duration)
            $('#recipe-kcals').html(R.message.rt_recipe_calories)
            $('#modal').css('visibility', 'visible').fadeIn(200)
        });
    })
    HookAction('#Logout', 'logout', [], function(response) {
        let R = JSON.parse(response)
        window.location.href = "?page=login"
    }, function(response) {}, () => {});
    HookAction('#Login', 'login', ['username', 'password'], function(response) {
        let R = JSON.parse(response)
        $('.spinner').css('visibility', 'hidden')
        window.location.href = "?page=home"
    }, function(response) {
        let R = JSON.parse(response)
        $('.spinner').css('visibility', 'hidden')
        $('.notice').html(R.message)
        setTimeout(() => {
            $('.notice').html("")
        }, 3000)
    }, () => {
        $('.spinner').css('visibility', 'visible')
    });
    // HookAction('#Add', 'add', Values('lang_input', 'lang'))

    $('#uploadFormFile').on('change', function(event) {
        var target = event.target || event.srcElement;
        if (target.value.length == 0) {
            if (numFiles == target.files.length) {}
        } else {
            numFiles = target.files.length;
            filename = target.value;
            var file_data = $('#uploadFormFile').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('for_id', window.lastSelected);
            form_data.append('for', $(this).data('for'));
            form_data.append('action', 'upload');
            $.ajax({
                url: '',
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    let r = JSON.parse(response)
                    let src = r.message
                    $(window.resultElement).prop('src', src)
                }
            });
        }
    })
})


prepUpload = function(id, resultElement) {
    window.lastSelected = id
    window.resultElement = resultElement
    $('#uploadFormFile').click()
}