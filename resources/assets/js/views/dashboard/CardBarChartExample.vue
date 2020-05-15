<script>
    import { Bar, mixins } from 'vue-chartjs';

    export default {
        extends: Bar,
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
                                display: false,
                                categoryPercentage: 1,
                                barPercentage: 0.5
                            }],
                            yAxes: [{
                                display: false
                            }]
                        }
                    };
                }
            },
        },
        mounted () {
            this.renderChart(this.chartData, this.options);
        }
    };
</script>
