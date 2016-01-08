/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    //Daterangepicker
    var endDate = new Date();
    var strendDate = endDate.getDate() + "/" + endDate.getMonth() + "/" + endDate.getFullYear();

    if ($('#reservation').length){
        $('#reservation').daterangepicker({
            "startDate": "01/01/2015",
            "endDate": strendDate,
            "opens": "left",
            locale: {
                format: 'DD/MM/YYYY'
            }
        }, function (start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        });
    }

    //Add icon Sort
    if ($('.header>th').length>0){
        
        $('.header>th').click(function () {
            var i = $(this).index();
            $('.header>th').each(function () {
                if (i !== $(this).index()) {
                    $(this).css("color", "");
                    $(this).removeClass("sorting_asc");
                    $(this).removeClass("sorting_desc");
                }
            });

            $(this).css("color", "#000");
            if (!$(this).hasClass("sorting_asc")) {
                $(this).removeClass("sorting_desc");
                $(this).addClass("sorting_asc");
            }
            else {
                $(this).removeClass("sorting_asc");
                $(this).addClass("sorting_desc");
            }

        });
        $('.header>th').get(1).click();
    }
});