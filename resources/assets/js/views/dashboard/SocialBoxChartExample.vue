<script>
    import { Line, mixins } from 'vue-chartjs';

    export default {
        extends: Line,
        mixins: [mixins.reactiveProp],
        props: {
            height: {
                Type: [String, Number],
                default: 0
            },
            options: {
                Type: Object,
                default () {
                    return {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                display: false
                            }],
                            yAxes: [{
                                display: false
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
                    };
                }
            }
        },
        mounted () {
            this.renderChart(this.chartData, this.options);
        }
    };
</script>
