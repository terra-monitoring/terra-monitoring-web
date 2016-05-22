google.charts.load('current', {packages:['line','corechart'], language:'de'});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

    // set options
    var options = {
        width: '80%',
        height: 500,
        hAxis: {
            textPosition: 'none'
        },
        crosshair:{
            trigger: 'focus',
            orientation: 'vertical',
            color: 'grey'
        },
        focusTarget: 'category',
        legend: { 
            position: 'bottom'
        },
        
    };


    //Temperatur
    var jsonData = $.ajax({
        url: "getData/getDataTemperature.php",
        data: {table: "daily", query: "seven_day"},
        type: "POST",
        dataType:"json",
        async: false
    }).responseText;
    var data = new google.visualization.DataTable(jsonData);
    var chart = new google.visualization.LineChart(document.getElementById('linechart_material_temp'));
    chart.draw(data, options);

    //Luftfeuchtigkeit
    var jsonData_s4 = $.ajax({
        url: "getData/getDataHumidity.php",
        data: {table: "daily", query: "seven_day"},
        type: "POST",
        dataType:"json",
        async: false
    }).responseText;
    var data_s4 = new google.visualization.DataTable(jsonData_s4);
    var chart_s4 = new google.visualization.LineChart(document.getElementById('linechart_material_hum'));
    chart_s4.draw(data_s4, options);
}