HookAction = function(triggerElem, action, values, successCallback = null, failureCallback = null, onClick = false) {
    $(triggerElem).on('click', function(e) {
        e.preventDefault()
        let data = {}
        let error = false
        values.forEach((item) => {
            data[item] = $(`#${item}`).val()
            if (data[item].length == 0) error = true
        })
        if (!error) Action(action, data, successCallback, failureCallback, onClick);
        else alert("! [app.js:HookAction]")

    })
}
Action = function(action, data, successCallback = null, failureCallback = null, onClick = null) {
    data.action = action

    if (onClick !== null)
        onClick()
    $.post('', data, function(response) {
        let r = JSON.parse(response)
        if (r.status == "success") {
            if (successCallback !== null)
                successCallback(response)
        } else {
            if (failureCallback !== null)
                failureCallback(response)

        }
    })
}
Values = function(className, dataId) {
    let values = []
    $(`.${className}`).each((i, element) => {
        let lang = $(element).data(dataId)
        values.push(lang)
    });
    return values;
}
readURL = function(input, elem) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $(elem).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}