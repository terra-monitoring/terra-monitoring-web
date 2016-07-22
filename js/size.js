/**
 * Created by Andre on 22.07.2016.
 */
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
        }
    };
    

    //Werte
    var jsonData_size = $.ajax({
        url: "getData/getDataSize.php",
        type: "POST",
        dataType:"json",
        async: false
    }).responseText;
    var data_size = new google.visualization.DataTable(jsonData_size);
    var chart_size = new google.visualization.LineChart(document.getElementById('linechart_material_size'));
    chart_size.draw(data_size, options);
}