<script>
    import { Line, mixins } from 'vue-chartjs';

    // const brandPrimary = '#20a8d8'
    const brandSuccess = '#4dbd74';
    const brandInfo = '#63c2de';
    const brandDanger = '#f86c6b';

    function convertHex (hex, opacity) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);

        const result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
        return result;
    }

    function random (min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    export default {
        extends: Line,
        mixins: [mixins.reactiveProp],
        props: ['height','labels','datasets', 'yAxes'],
        mounted () {
            var elements = 27;
            var data1 = [];
            var data2 = [];
            var data3 = [];

            for (var i = 0; i <= elements; i++) {
                data1.push(random(50, 200));
                data2.push(random(80, 100));
                data3.push(65);
            }
            this.renderChart(this.chartData, {
                maintainAspectRatio: false,
                legend: {
                    display: true
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            drawOnChartArea: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            stepSize: this.yAxes.stepSize,
                            max: this.yAxes.max
                        },
                        gridLines: {
                            display: true
                        }
                    }]
                },
                elements: {
                    point: {
                        radius: 0,
                        hitRadius: 10,
                        hoverRadius: 4,
                        hoverBorderWidth: 3
                    }
                }
            });
        }
    };
</script>
