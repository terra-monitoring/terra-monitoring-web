google.charts.load('current', {packages:['line','corechart'], language:'de'});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

    // set options
    var options = {
        width: '80%',
        height: 250,
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
    
    /*
     * sensor temp top
     */
    var jsonData_s1 = $.ajax({
        url: "getData/getDataTemperature.php",
        data: {table: "total", query: "s1"},
        type: "POST",
        dataType: "json",
        async: false
    }).responseText;
    var data_s1 = new google.visualization.DataTable(jsonData_s1);
    var chart_s1 = new google.visualization.LineChart(document.getElementById('linechart_material_s1'));
    chart_s1.draw(data_s1, options);
    
    /*
     * sensor temp center
     */
    var jsonData_s2 = $.ajax({
        url: "getData/getDataTemperature.php",
        data: {table: "total", query: "s2"},
        type: "POST",
        dataType: "json",
        async: false
    }).responseText;
    var data_s2 = new google.visualization.DataTable(jsonData_s2);
    var chart_s2 = new google.visualization.LineChart(document.getElementById('linechart_material_s2'));
    chart_s2.draw(data_s2, options);

    /*
     * sensor temp bottom
     */
    var jsonData_s3 = $.ajax({
        url: "getData/getDataTemperature.php",
        data: {table: "total", query: "s3"},
        type: "POST",
        dataType: "json",
        async: false
    }).responseText;
    var data_s3 = new google.visualization.DataTable(jsonData_s3);
    var chart_s3 = new google.visualization.LineChart(document.getElementById('linechart_material_s3'));
    chart_s3.draw(data_s3, options);
    
    /*
     * sensor hum
     */
    var jsonData_s4 = $.ajax({
        url: "getData/getDataHumidity.php",
        data: {table: "total", query: "s4"},
        type: "POST",
        dataType: "json",
        async: false
    }).responseText;
    var data_s4 = new google.visualization.DataTable(jsonData_s4);
    var chart_s4 = new google.visualization.LineChart(document.getElementById('linechart_material_s4'));
    chart_s4.draw(data_s4, options);
}