/*----------------------  CHARTS  ----------------------- */
Highcharts.setOptions({
    chart: {
        style: {
            fontFamily: 'Roboto Th'
        },
        zoomType: false
    }
});
Highcharts.getOptions().plotOptions.pie.colors = (function () {
    var colors = [],
        base = '#039BE5',
        i;

    for (i = 0; i < 10; i += 1) {
        // Start out with a darkened base color (negative brighten), and end
        // up with a much brighter color
        colors.push(Highcharts.Color(base).brighten((i - 2) / 64).get());
    }
    return colors;
}());
var map = Highcharts.mapChart('map', {
    chart: {
        map: 'custom/world',
        backgroundColor: 'transparent',
        animation: false
    },
    title: {
        text: '',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        borderColor: '#FFFFFF',
        borderRadius: 2,
        borderWidth: 1
    },
    mapNavigation: {
        enabled: false,
        buttonOptions: {
            verticalAlign: 'bottom'
        }
    },
    series: [{
        name: 'Countries',
        enableMouseTracking: false
    }]
});

var countries = Highcharts.chart('countries', {
    chart: {
        type: 'bar',
        animation: false
    },
    title: {
        text: ''
    },
    xAxis: {
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        borderColor: '#F7F7F7',
        borderRadius: 2,
        borderWidth: 1,
        padding: 15,
        animation: true,
        valueDecimals: false,
        formatter: function () {
            return '<b>' + this.y + '</b> Players from <b>' + this.x + '</b>';
        }
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        },
        column: {
            stacking: 'percent'
        }
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    }
});
var future = Highcharts.chart('future', {
    chart: {
        type: 'spline',
        backgroundColor: 'transparent',
        height: 200,
    },
    title: {
        text: ''
    },
    yAxis: {
        title: {
            text: ''
        },
        gridLineWidth: 0,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            }
        }
    },
    xAxis: {
        gridLineWidth: 0,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            }
        },
        type: 'datetime',
        events: {
            afterSetExtremes: function (event) {
                var date = new Date(event.min);
                var datevalues = date.getFullYear()
                    + '-' + date.getMonth() + 1
                    + '-' + date.getDate()
                    + ' ' + date.getUTCHours()
                    + ':' + date.getMinutes()
                    + ':' + date.getSeconds();
                $("#timestamp").text(datevalues);
            }
        }
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        borderColor: '#F7F7F7',
        borderRadius: 2,
        borderWidth: 1,
        pointFormat: "<b>{point.y:.0f}</b> Players"
    },
    plotOptions: {
        series: {
            pointStart: 2010,
            fillOpacity: 0.1,
            turboThreshold: 1000,
            marker: {
                enabled: false
            }
        }
    }
});
var stats = Highcharts.chart('container', {
    chart: {
        type: 'areaspline',
        backgroundColor: 'transparent',
        height: 200,
        zoomType: 'x'
    },
    title: {
        text: ''
    },
    scrollbar: {
        enabled: false
    },
    navigator: {
        enabled: false
    },
    yAxis: {
        title: {
            text: ''
        },
        gridLineWidth: 0,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            }
        }
    },
    xAxis: {
        gridLineWidth: 0,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            }
        },
        type: 'datetime',
        events: {
            afterSetExtremes: function (event) {
                var date = new Date(event.min);
                var datevalues = date.getFullYear()
                    + '-' + date.getMonth() + 1
                    + '-' + date.getDate()
                    + ' ' + date.getUTCHours()
                    + ':' + date.getMinutes()
                    + ':' + date.getSeconds();
                $("#timestamp").text(datevalues);
            }
        }
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        borderColor: '#F7F7F7',
        borderRadius: 2,
        borderWidth: 1
    },
    plotOptions: {
        series: {
            pointStart: 2010,
            fillOpacity: 0.1,
            marker: {
                enabled: false
            }
        }
    }
});
var retention = Highcharts.chart({
    chart: {
        type: 'spline',
        backgroundColor: 'transparent',
        renderTo: 'retention',
        height: 160,
        animation: false
    },
    title: {
        text: ''
    },
    yAxis: {
        title: {
            text: ''
        },
        gridLineWidth: 1,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            },
            formatter: function () {
                return +this.value + '%';
            }
        },
        min: 0,
        tickInterval: 50,
        max: 100
    },
    xAxis: {
        gridLineWidth: 0,
        minorGridLineWidth: 0,
        labels: {
            style: {
                color: '#B6B6B6',
                fontSize: '12px'
            }
        },
        categories: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        max: 6
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        borderColor: '#F7F7F7',
        borderRadius: 2,
        borderWidth: 1,
        padding: 15,
        animation: true,
        valueDecimals: false,
        valueSuffix: '%'
    },
    plotOptions: {
        series: {
            // stacking: 'percent',
            fillOpacity: 0.1,
            marker: {
                enabled: false
            }
        }
    }
});
var premium = null;
$.ajax({
    url: '../inc/php/dep/pages/analytics.php?dataload=is_enabled&variable=setting_servermode',
    type: "get",
    success: function (data) {
        if (data === 'true') {
            premium = Highcharts.chart({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    renderTo: 'premium',
                    height: 407
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '<b>{point.percentage:.1f}%</b>',
                    backgroundColor: '#FFFFFF',
                    borderColor: '#FFFFFF',
                    borderRadius: 2,
                    borderWidth: 1
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: false,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
                        color: '#039BE5'
                    },
                    series: {
                        color: '#5053D3'
                    }
                },
                credits: {
                    enabled: false
                }
            });
            $.ajax({
                url: "../inc/php/dep/charts/analytics.php?type=premium",
                type: "get",
                success: function (data) {
                    premium.addSeries({
                        name: this.point,
                        colorByPoint: true,
                        data: JSON.parse(data)
                    });
                }
            });
        }
    }
});
/* -----------------------  LOADING LIVE DATA ---------------------------- */
$.ajax({
    url: "../inc/php/dep/charts/analytics.php?type=map",
    type: "get",
    dataType: "json",
    success: function (data) {
        map.addSeries({
            type: 'mapbubble',
            name: 'Newest Players',
            joinBy: ['iso-a2', 'code'],
            data: data,
            minSize: 4,
            color: '#2196F3',
            maxSize: '20%',
            tooltip: {
                pointFormat: '<b>{point.name}</b>: {point.z} players',
                headerFormat: ''
            }
        });
    }
});
$.ajax({
    url: "../inc/php/dep/charts/analytics.php?type=countrynames",
    type: "get",
    dataType: 'json',
    success: function (data) {
        countries.xAxis[0].setCategories(data);
    }
});
$.ajax({
    url: "../inc/php/dep/charts/analytics.php?type=countrydata",
    type: "get",
    dataType: "json",
    success: function (data) {
        countries.addSeries({
            name: this.point,
            data: data,
            color: '#2196F3'
        });
    }
});
$.ajax({
    url: "../inc/php/dep/charts/analytics.php?type=retention",
    type: "get",
    dataType: "json",
    success: function (data) {
        retention.addSeries({
            name: this.point,
            data: data,
            color: '#2196F3'
        });
    }
});
/* ----------------------- STOCK CHART --------------------------------- */
$.ajax({
    url: "../inc/php/dep/charts/analytics.php?type=future",
    type: "get",
    dataType: "json",
    success: function (data) {
        future.addSeries({
            name: 'Estimated Total Players',
            color: '#2196F3',
            data: data
        });
    }
});
$.ajax({
    url: '../inc/php/dep/pages/analytics.php?dataload=onlineplayers',
    type: 'get',
    dataType: 'json',
    success: function (data) {
        stats.addSeries({
            name: 'Returning Players',
            color: '#2196F3',
            data: data
        });
    }
});

var isAnalyticsLoaded = true;