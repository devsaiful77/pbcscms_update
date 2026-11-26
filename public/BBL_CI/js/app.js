function openURL(route) {
    //window.open(route);
    $('.loadingOverlay').removeClass('loader-none');
    // setTimeout(function () {
    //     $('.loadingOverlay').removeClass('loader-none');
    // }, 500);
    window.location.href = route;
}
function textInEnglish(event) {
    let inputValue = event.target;
    // var regex = /^[A-Za-z0-9\s.,:!?@#$%^&*()\-+=_]*$/;
    var regex = /^[A-Za-z0-9\s.,:!?@#$%^&*()\-+=_'"]*$/;
    if (!regex.test(inputValue.value)) {
        // inputValue.value = inputValue.value.replace(/[^A-Za-z0-9\s.,:!?@#$%^&*()\-+=_]/g, '');
        inputValue.value = inputValue.value.replace(/[^A-Za-z0-9\s.,:!?@#$%^&*()\-+=_'"]/g, '');
    }
}

function allowOnlyNumbers(event) {
    let inputValue = event.target;
    var regex = /^[0-9]*$/;
    if (!regex.test(inputValue.value)) {
        inputValue.value = inputValue.value.replace(/[^0-9]/g, '');
    }
}