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
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    color: 'transparent',
                                    zeroLineColor: 'transparent'
                                },
                                ticks: {
                                    fontSize: 2,
                                    fontColor: 'transparent'
                                }
                            }],
                            yAxes: [{
                                display: false,
                                ticks: {
                                    display: false,
                                }
                            }]
                        },
                        elements: {
                            line: {
                                borderWidth: 1
                            },
                            point: {
                                radius: 4,
                                hitRadius: 10,
                                hoverRadius: 4
                            }
                        }
                    }
                }
            }
        },
        mounted () {
            this.renderChart(this.chartData, this.options);
        }
    };
</script>
