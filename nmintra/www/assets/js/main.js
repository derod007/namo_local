$.extend(true, $.fn.DataTable.defaults, {
    "searching": false,
    "ordering": true,
    // "column": {
    // "orderable": false
    // },
    "info": true,
    "paging": false,
    "dom": '<"top">rt<"bottom"<"col-sm-5"i><"col-sm-7"p>><"clear">',
    "lengthChange": false,
    "pageLength": 20,
    "language": {
        "sEmptyTable": "데이터가 없습니다",
        "sInfo": "_START_ ~ _END_ / 전체: _TOTAL_",
        "sInfoEmpty": "0 - 0 / 0",
        "sInfoFiltered": "(총 _MAX_ 개)",
        "sInfoPostFix": "",
        "sInfoThousands": ",",
        "sLengthMenu": "_MENU_",
        "sLoadingRecords": "읽는중...",
        "sProcessing": "처리중...",
        "sSearch": "검색:",
        "sZeroRecords": "검색 결과가 없습니다",
        "oPaginate": {
            "sFirst": "처음",
            "sLast": "마지막",
            "sNext": "다음",
            "sPrevious": "이전"
        },
        "oAria": {
            "sSortAscending": ": 오름차순 정렬",
            "sSortDescending": ": 내림차순 정렬"
        }
    }
});

$.fn.select2.defaults.set("theme", "bootstrap");

var commonjs = {};
commonjs.selectNav = function (navId, elId) {
    $('#' + navId).find('li.active').removeClass('active');
    $('#' + elId).parents('li.dropdown').addClass('active');
    $('#' + elId).addClass('active');
};

$(function () {

    $('#btnToggleContainer').click(function (e) {
        e.preventDefault();

		$('#nav_container').removeClass('container');
		$('#wrap-content').removeClass('container');
		$('#nav_container').addClass('no-container');
		$('#wrap-content').addClass('no-container');

        $('.dataTable').DataTable().draw();
    });

    $('.max-768-toggle').click(function () {

        var target = ".max-768-target";

        if ($(this).attr("target")) {
            target = $(this).attr("target");
        }

        $(target).each(function (idx, el) {
            var $el = $(el);
            if ($el.hasClass("max-768-show") === false) {
                $el.addClass("max-768-show")
            } else {
                $el.removeClass("max-768-show")
            }
        });

    });


});

$(function () {
    $.datepicker.regional["ko"] = {
        closeText: "닫기",
        prevText: "이전달",
        nextText: "다음달",
        currentText: "오늘",
        monthNames: ["1월(JAN)", "2월(FEB)", "3월(MAR)", "4월(APR)", "5월(MAY)", "6월(JUN)", "7월(JUL)", "8월(AUG)", "9월(SEP)", "10월(OCT)", "11월(NOV)", "12월(DEC)"],
        monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        dayNames: ["일", "월", "화", "수", "목", "금", "토"],
        dayNamesShort: ["일", "월", "화", "수", "목", "금", "토"],
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        weekHeader: "Wk",
        dateFormat: "yy-mm-dd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
    $.datepicker.setDefaults($.datepicker.regional["ko"]);
    $(".datepicker").attr("autocomplete", "off");
});

$(document).ready(function(){
    //apply on typing and focus
    $('input.currency').on('blur',function(){
        $(this).santizeCommas();
    });
    //then sanatize on leave
  // if sanitizing needed on form submission time, 
  //then comment beloc function here and call in in form submit function.
    $('input.currency').on('focus',function(){
       $(this).manageCommas();
    });
    $('input.currency').on('keyup',function(){
       $(this).manageCommas();
    });
	$('input.currency').keydown(function() {
		if (event.keyCode === 13) {
			event.preventDefault();
		}
	});
	
	

});

// 검색폼 엔터
$(function () {
	$('#fsearch input[type=text]').keydown(function() {
		if (event.keyCode === 13) {
			//console.log(event);
			$('#search').click();
			return false;
		}
	});
});

String.prototype.addComma = function() {
  return this.replace(/(.)(?=(.{3})+$)/g,"$1,")
}
//Jquery global extension method
$.fn.manageCommas = function () {
    return this.each(function () {
        $(this).val($(this).val().replace(/(,| )/g,'').addComma());
    })
}

$.fn.santizeCommas = function() {
  return $(this).val($(this).val().replace(/(,| )/g,''));
}
	