(function ($) {
 "use strict";

		$(".chosen")[0] && $(".chosen").chosen({
            width: "100%",
            allow_single_deselect: !0
        });
		/*--------------------------
		 auto-size Active Class
		---------------------------- */	
		$(".auto-size")[0] && autosize($(".auto-size"));
		/*--------------------------
		 Collapse Accordion Active Class
		---------------------------- */	
		$(".collapse")[0] && ($(".collapse").on("show.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        }), $(".collapse").on("hide.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").removeClass("active")
        }), $(".collapse.in").each(function() {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        }));
		/*----------------------------
		 jQuery tooltip
		------------------------------ */
		$('[data-toggle="tooltip"]').tooltip();
		/*--------------------------
		 popover
		---------------------------- */	
		$('[data-toggle="popover"]')[0] && $('[data-toggle="popover"]').popover();
		/*--------------------------
		 File Download
		---------------------------- */	
		$('.btn.dw-al-ft').on('click', function(e) {
			e.preventDefault();
		});
		/*--------------------------
		 Sidebar Left
		---------------------------- */	
		$('#sidebarCollapse').on('click', function () {
			 $('#sidebar').toggleClass('active');
			 
		 });
		$('#sidebarCollapse').on('click', function () {
			$("body").toggleClass("mini-navbar");
			SmoothlyMenu();
		});
		$('.menu-switcher-pro').on('click', function () {
			var button = $(this).find('i.nk-indicator');
			button.toggleClass('notika-menu-befores').toggleClass('notika-menu-after');
			
		});
		$('.menu-switcher-pro.fullscreenbtn').on('click', function () {
			var button = $(this).find('i.nk-indicator');
			button.toggleClass('notika-back').toggleClass('notika-next-pro');
		});
		/*--------------------------
		 Button BTN Left
		---------------------------- */	
		
		$(".nk-int-st")[0] && ($("body").on("focus", ".nk-int-st .form-control", function() {
            $(this).closest(".nk-int-st").addClass("nk-toggled")
        }), $("body").on("blur", ".form-control", function() {
            var p = $(this).closest(".form-group, .input-group"),
                i = p.find(".form-control").val();
            p.hasClass("fg-float") ? 0 == i.length && $(this).closest(".nk-int-st").removeClass("nk-toggled") : $(this).closest(".nk-int-st").removeClass("nk-toggled")
        })), $(".fg-float")[0] && $(".fg-float .form-control").each(function() {
            var i = $(this).val();
            0 == !i.length && $(this).closest(".nk-int-st").addClass("nk-toggled")
        });
	
	/*----------------------------
	 jQuery MeanMenu
	------------------------------ */
	jQuery('nav#dropdown').meanmenu();
	
	/*----------------------------
	 wow js active
	------------------------------ */
	 new WOW().init();
	 
	/*--------------------------
	 scrollUp
	---------------------------- */	
	$.scrollUp({
        scrollText: '<i class="fa fa-angle-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });

		var departmentId = $('#department_id', '.container-fluid');
		var groupId = $('#group_id', '.container-fluid');
		var subgroupId = $('#subgroup_id', '.container-fluid');
		var issueType = $('#issueType', '.container-fluid');
		var issueItems = $('#issueItems', '.container-fluid');

		departmentId.on('change', function () {
			var departmentID = $(this).val();
			getGroupsOptions(departmentID);
		});
		groupId.on('change', function () {
			var groupID = $(this).val();
			getSubgroupsOptions(groupID);
		});


		var getGroupsOptions = function (departmentID) {
			if (departmentID) {
				$.ajax({
					url: base_url+'/group-list/' + departmentID,
					type: "GET",
					dataType: "json",
					success: function (data) {
						groupId.html('<option value="">Select Group</option>');
						subgroupId.html('<option value="">Select Group first</option>');
						$.each(data, function (key, value) {
							groupId.append('<option value="' + value.id + '">' + value.name + '</option>');
						});
					}
				});
			} else {
				groupId.html('<option value="">Select Group </option>');
				subgroupId.html('<option value="">Select Group first</option>');
			}
		};

		var getSubgroupsOptions = function (groupID) {
			if (groupID) {
				$.ajax({
					url: base_url+'/subgroup-list/' + groupID,
					type: "GET",
					dataType: "json",
					success: function (data) {
						subgroupId.html('<option value="">Select Subgroup</option>');
						$.each(data, function (key, value) {
							subgroupId.append('<option value="' + value.id + '">' + value.name + '</option>');
						});
					}
				});
			} else {
				groupID.html('<option value="">Select Group first</option>');
			}
		};


 
})(jQuery); 