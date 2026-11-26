/****************** Print Div Content  ******************/ 

function printDivContent(divId)
{
	var  data = $("#"+divId).html(); var  title = $(".widgettitle").text(); var mywindow = window.open('', 'new div', 'height=400,width=600'); mywindow.document.write('<html><head><title>'+title+'</title>'); mywindow.document.write('<style>.to_be_hidden{display:none;}</style>'); mywindow.document.write('<style>.table{border:1px;}</style>'); /*optional stylesheet*/ /*mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');*/ mywindow.document.write('</head><body >'); mywindow.document.write(data); mywindow.document.write('</body></html>'); mywindow.print(); mywindow.close(); return true; 
}

function cancel(controller)
{
	var msg = "Do you want to back ? ";
	var msgContent = "If you go back then all modified data will be lost. ";
	/* var r = confirm(msg); if (r == true) {//history.back(); window.open(base_url+controller, '_self'); }*/
	$.confirm({
	    title: msg,
	    content: msgContent,
	    type : 'red',
	    buttons: {
	        Yes: function () {
	        	window.open(base_url+controller, '_self');
	        },
	        No: function () {
	        }
	    }
	});
}

function back(controller)
{
	var msg = "Do you want to back ? ";
	var msgContent = "If you go back then all modified data will be lost. ";
	/* var r = confirm(msg); if (r == true) {//history.back(); window.open(base_url+controller, '_self'); }*/
	$.confirm({
	    title: msg,
	    content: msgContent,
	    type : 'red',
	    buttons: {
	        Yes: function () {
	        	window.open(base_url+controller, '_self');
	        },
	        No: function () {
	        }
	    }
	});
}

function customAlert(title,msg,type)
{
	$.alert({title:title,content:msg,closeIcon:true,type:type});
}

function redirectUrl(controllerName)
{
	window.location = base_url+controllerName;
}

$(document).on('paste drop', '.preventPaste', function(event) { 
  event.preventDefault();
});


function overlay(event) {
    if (event == 'show') {
        $('.overlay').show();
    } else {
        $('.overlay').hide();
    }
};

$(".select2").select2({placeholder: "Please Select"});


var todateObj = new Date();
var toDate = todateObj.getDate();
var toMonth = todateObj.getMonth();
var toYear = todateObj.getFullYear();

$('.datePickerFrmToday').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    minDate: new Date(toYear,toMonth,toDate)
});

$('.datePicker').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true
});
flatpickr(".datePickerLoguser", {
    dateFormat: "Y-m-d",
    changeYear: true,
    changeMonth: true
});
// $('.srvDatePicker').datepicker({
//     dateFormat: 'yy-mm-dd',
//     changeYear: true,
//     changeMonth: true
// });
flatpickr(".srvDatePicker", {
    dateFormat: "Y-m-d",
    changeYear: true,
    changeMonth: true
});

$('.datePickerThree').datepicker({
    minDate: -90, 
    maxDate: new Date(toYear,toMonth,toDate),
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true
});

var isInitialLoad = true;
$('.datePickerThreeFrom').flatpickr({
    dateFormat: "d-m-Y",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoad) {
            var fromDateObj = selectedDates[0];
            var toDatePicker = document.querySelector('.datePickerThreeTo')._flatpickr;

            toDatePicker.set('minDate', fromDateObj);
            toDatePicker.set('maxDate', new Date(fromDateObj.getTime() + (30 * 24 * 60 * 60 * 1000)));

            if ($('.datePickerThreeTo').val() === "") {
                toDatePicker.setDate(dateStr);
            }
        }
    },
    onReady: function() {
        setTimeout(() => { isInitialLoad = false; }, 500);
    }
});

$('.datePickerThreeTo').flatpickr({
    dateFormat: "d-m-Y",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoad) {
            var toDateObj = selectedDates[0];
            var fromDatePicker = document.querySelector('.datePickerThreeFrom')._flatpickr;

            fromDatePicker.set('minDate', new Date(toDateObj.getTime() - (30 * 24 * 60 * 60 * 1000)));
            fromDatePicker.set('maxDate', toDateObj);

            if ($('.datePickerThreeFrom').val() === "") {
                fromDatePicker.setDate(dateStr);
            }
        }
    },
});


// date picker one day for history report
var isInitialLoadHistory = true;
$('.datePickerHistoryFrom').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadHistory) {
            var fromDateObj = selectedDates[0]; // Selected 'From' date
            var toDatePicker = document.querySelector('.datePickerHistoryTo')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'To' date picker
            toDatePicker.set('minDate', fromDateObj);
            toDatePicker.set('maxDate', fromDateObj);

            // Default value for 'To' picker if empty
            if ($('.datePickerHistoryTo').val() === "") {
                $('.datePickerHistoryTo').val(dateStr);
            }
        }
    },
    onReady: function() {
        setTimeout(() => { isInitialLoadHistory = false; }, 500);
    }
});

$('.datePickerHistoryTo').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadHistory) {
            var toDateObj = selectedDates[0]; // Selected 'To' date
            var fromDatePicker = document.querySelector('.datePickerHistoryFrom')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'From' date picker
            fromDatePicker.set('minDate', toDateObj);
            fromDatePicker.set('maxDate', toDateObj);

            // Default value for 'From' picker if empty
            if ($('.datePickerHistoryFrom').val() === "") {
                $('.datePickerHistoryFrom').val(dateStr);
            }
        }
    },
});


// date picker 7 day for API update report
var isInitialLoadApi = true;
$('.datePickerApiFrom').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadApi) {
            var fromDateObj = selectedDates[0]; // Selected 'From' date
            
            // Calculate 'To' date as 6 days ahead
            var toDateObj = new Date(fromDateObj);
            toDateObj.setDate(fromDateObj.getDate() + 6);

            var toDatePicker = document.querySelector('.datePickerApiTo')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'To' date picker
            toDatePicker.set('minDate', fromDateObj);
            toDatePicker.set('maxDate', toDateObj);

            // Set default value for 'To' picker if empty
            if ($('.datePickerApiTo').val() === "") {
                $('.datePickerApiTo').val(dateStr);
            }
        }
    },
    onReady: function() {
        setTimeout(() => { isInitialLoadApi = false; }, 500);
    }
});

$('.datePickerApiTo').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadApi) {
            var toDateObj = selectedDates[0]; // Selected 'To' date
            
            // Calculate 'From' date as 6 days before
            var fromDateObj = new Date(toDateObj);
            fromDateObj.setDate(toDateObj.getDate() - 6);

            var fromDatePicker = document.querySelector('.datePickerApiFrom')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'From' date picker
            fromDatePicker.set('minDate', fromDateObj);
            fromDatePicker.set('maxDate', toDateObj);

            // Set default value for 'From' picker if empty
            if ($('.datePickerApiFrom').val() === "") {
                $('.datePickerApiFrom').val(dateStr);
            }
        }
    },
});

var isInitialLoadFive = true;
$('.datePickerFiveFrom').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadFive) {
            var fromDateObj = selectedDates[0]; // Selected 'From' date
            
            // Calculate 'To' date as 90 days ahead
            var toDateObj = new Date(fromDateObj);
            toDateObj.setDate(fromDateObj.getDate() + 90);

            var toDatePicker = document.querySelector('.datePickerFiveTo')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'To' date picker
            toDatePicker.set('minDate', fromDateObj);
            toDatePicker.set('maxDate', toDateObj);

            // Set default value for 'To' picker if empty
            if ($('.datePickerFiveTo').val() === "") {
                $('.datePickerFiveTo').val(dateStr);
            }
        }
    },
    onReady: function() {
        setTimeout(() => { isInitialLoadFive = false; }, 500);
    }
});

$('.datePickerFiveTo').flatpickr({
    dateFormat: "d-m-Y", // Match the desired date format
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadFive) {
            var toDateObj = selectedDates[0]; // Selected 'To' date
            
            // Calculate 'From' date as 90 days before
            var fromDateObj = new Date(toDateObj);
            fromDateObj.setDate(toDateObj.getDate() - 90);

            var fromDatePicker = document.querySelector('.datePickerFiveFrom')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'From' date picker
            fromDatePicker.set('minDate', fromDateObj);
            fromDatePicker.set('maxDate', toDateObj);

            // Set default value for 'From' picker if empty
            if ($('.datePickerFiveFrom').val() === "") {
                $('.datePickerFiveFrom').val(dateStr);
            }
        }
    },
});

var isInitialLoadFour = true;
$('.datePickerFourFrom').flatpickr({
    dateFormat: "d-m-Y", // Date format equivalent to 'dd-mm-yy'
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadFour) {
            var fromDateObj = selectedDates[0]; // Selected 'From' date
            
            // Calculate the 'To' date range as up to 120 days ahead
            var toDateObj = new Date(fromDateObj);
            toDateObj.setDate(fromDateObj.getDate() + 120);

            var toDatePicker = document.querySelector('.datePickerFourTo')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'To' picker
            toDatePicker.set('minDate', fromDateObj);
            toDatePicker.set('maxDate', toDateObj);

            // Set default value for 'To' picker if empty
            if ($('.datePickerFourTo').val() === "") {
                $('.datePickerFourTo').val(dateStr);
            }
        }
    },
    onReady: function() {
        setTimeout(() => { isInitialLoadFour = false; }, 500);
    }
});

$('.datePickerFourTo').flatpickr({
    dateFormat: "d-m-Y", // Date format equivalent to 'dd-mm-yy'
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0 && !isInitialLoadFour) {
            var toDateObj = selectedDates[0]; // Selected 'To' date
            
            // Calculate the 'From' date range as up to 120 days before
            var fromDateObj = new Date(toDateObj);
            fromDateObj.setDate(toDateObj.getDate() - 120);

            var fromDatePicker = document.querySelector('.datePickerFourFrom')._flatpickr;

            // Set 'minDate' and 'maxDate' for 'From' picker
            fromDatePicker.set('minDate', fromDateObj);
            fromDatePicker.set('maxDate', toDateObj);

            // Set default value for 'From' picker if empty
            if ($('.datePickerFourFrom').val() === "") {
                $('.datePickerFourFrom').val(dateStr);
            }
        }
    },
});



Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
}



function backButtonPress(backModuleUrl) {
    $.confirm({
        title: 'Cancel',
        content: 'Do you want to go back?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: 'Yes',
                btnClass: 'btn-red',
                action: function(){
                    $(location).attr('href', base_url+'/'+backModuleUrl);
                }
            },
            No: function () {
            }
        }
    });
}

function customConfirm(title,msg,type,url) {
    $.confirm({
        title:title,
        content:msg,
        type:type,
        typeAnimated: true,
        buttons: {
            Yes: {
                text: 'YES',
                btnClass: 'btn-red',
                action: function(){
                    $(location).attr('href', url);
                }
            },
            No: {
	            text: 'NO'
	        }
        }
    });
}

function customUpdateConfirm(title,msg,type,id) {
    $.confirm({
        title:title,
        content:msg,
        type:type,
        typeAnimated: true,
        buttons: {
            Yes: {
                text: 'YES',
                btnClass: 'btn-green',
                action: function(){
                    document.getElementById(id).submit();
                }
            },
            No: {
                text: 'NO'
            }
        }
    });
}

function redirectToUrl(title,msg,type,url) {
    $.confirm({
        title:title,
        content:msg,
        type:type,
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: 'OK',
                btnClass: 'btn-success',
                action: function(){
                    $(location).attr('href', base_url+'/'+url);
                }
            },
        }
    });
}

$(document).on('mouseenter', '.hover-fullscreen-trigger', function () {
    const imageUrl = $(this).data('full');
    $('#fullscreen-hover-preview img').attr('src', imageUrl);
    $('#fullscreen-hover-preview').fadeIn(100);
});

$(document).on('mouseleave', '.hover-fullscreen-trigger', function () {
    $('#fullscreen-hover-preview').fadeOut(100);
});