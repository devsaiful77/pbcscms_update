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

$('.srvDatePicker').datepicker({
    dateFormat: 'yy-mm-dd',
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

$('.datePickerThreeFrom').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var fromDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerThreeTo').datepicker('option', 'minDate', fromDateObj );
        $('.datePickerThreeTo').datepicker('option', 'maxDate', fromDateObj.addDays(30) ); 
        if($('.datePickerThreeTo').val() == ""){
            $('.datePickerThreeTo').val($('.datePickerThreeFrom').val());
        }
    }
});

$('.datePickerThreeTo').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var toDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerThreeFrom').datepicker('option', 'minDate', toDateObj.addDays(-30) );
        $('.datePickerThreeFrom').datepicker('option', 'maxDate', toDateObj );
        if($('.datePickerThreeFrom').val() == ""){
            $('.datePickerThreeFrom').val($('.datePickerThreeTo').val());
        }
    }
});

// date picker one day for history report

$('.datePickerHistoryFrom').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function (dateText) {
        var dateArr = dateText.split("-");
        var fromDateObj = new Date(dateArr[2], (dateArr[1] - 1), dateArr[0]);
        $('.datePickerHistoryTo').datepicker('option', 'minDate', fromDateObj);
        $('.datePickerHistoryTo').datepicker('option', 'maxDate', fromDateObj);
        if ($('.datePickerHistoryTo').val() == "") {
            $('.datePickerHistoryTo').val($('.datePickerHistoryFrom').val());
        }
    }
});

$('.datePickerHistoryTo').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function (dateText) {
        var dateArr = dateText.split("-");
        var toDateObj = new Date(dateArr[2], (dateArr[1] - 1), dateArr[0]);
        $('.datePickerHistoryFrom').datepicker('option', 'minDate', toDateObj);
        $('.datePickerHistoryFrom').datepicker('option', 'maxDate', toDateObj);
        if ($('.datePickerHistoryFrom').val() == "") {
            $('.datePickerHistoryFrom').val($('.datePickerHistoryTo').val());
        }
    }
});

// date picker 7 day for API update report

$('.datePickerApiFrom').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function (dateText) {
        var dateArr = dateText.split("-");
        var fromDateObj = new Date(dateArr[2], (dateArr[1] - 1), dateArr[0]);

        // Calculate the toDate as 6 days ahead of fromDate
        var toDateObj = new Date(fromDateObj);
        toDateObj.setDate(fromDateObj.getDate() + 6);

        $('.datePickerApiTo').datepicker('option', 'minDate', fromDateObj);
        $('.datePickerApiTo').datepicker('option', 'maxDate', toDateObj);

        if ($('.datePickerApiTo').val() == "") {
            $('.datePickerApiTo').val($('.datePickerApiFrom').val());
        }
    }
});

$('.datePickerApiTo').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function (dateText) {
        var dateArr = dateText.split("-");
        var toDateObj = new Date(dateArr[2], (dateArr[1] - 1), dateArr[0]);

        // Calculate the fromDate as 6 days before toDate
        var fromDateObj = new Date(toDateObj);
        fromDateObj.setDate(toDateObj.getDate() - 6);

        $('.datePickerApiFrom').datepicker('option', 'minDate', fromDateObj);
        $('.datePickerApiFrom').datepicker('option', 'maxDate', toDateObj);

        if ($('.datePickerApiFrom').val() == "") {
            $('.datePickerApiFrom').val($('.datePickerApiTo').val());
        }
    }
});




$('.datePickerFiveFrom').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var fromDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerFiveTo').datepicker('option', 'minDate', fromDateObj );
        $('.datePickerFiveTo').datepicker('option', 'maxDate', fromDateObj.addDays(90) );
        if($('.datePickerFiveTo').val() == ""){
            $('.datePickerFiveTo').val($('.datePickerFiveFrom').val());
        }
    }
});
$('.datePickerFiveTo').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var toDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerFiveFrom').datepicker('option', 'minDate', toDateObj.addDays(-90) );
        $('.datePickerFiveFrom').datepicker('option', 'maxDate', toDateObj );
        if($('.datePickerFiveFrom').val() == ""){
            $('.datePickerFiveFrom').val($('.datePickerFiveTo').val());
        }
    }
});

$('.datePickerFourFrom').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var fromDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerFourTo').datepicker('option', 'minDate', fromDateObj );
        $('.datePickerFourTo').datepicker('option', 'maxDate', fromDateObj.addDays(120) ); 
        if($('.datePickerFourTo').val() == ""){
            $('.datePickerFourTo').val($('.datePickerFourFrom').val());
        }
    }
});

$('.datePickerFourTo').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true,
    changeMonth: true,
    onSelect: function(dateText) {
        var dateArr = dateText.split("-");
        var toDateObj = new Date(dateArr[2],(dateArr[1]-1),dateArr[0]);
        $('.datePickerFourFrom').datepicker('option', 'minDate', toDateObj.addDays(-120) );
        $('.datePickerFourFrom').datepicker('option', 'maxDate', toDateObj );
        if($('.datePickerFourFrom').val() == ""){
            $('.datePickerFourFrom').val($('.datePickerFourTo').val());
        }
    }
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