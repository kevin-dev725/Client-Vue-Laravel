<template>
    <div class="animated fadeIn">
        <b-row>
            <b-col>
                <b-card no-body
                        class="bg-primary">
                    <b-card-body class="pb-0">
                        <h4 class="mb-0">{{ data.users.count }}</h4>
                        <p>Registered users</p>
                    </b-card-body>
                    <card-line3-chart-example class="chart-wrapper"
                                              style="height:70px;"
                                              height="70"
                                              :chartData="data.users_weekly_data"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card no-body
                        class="bg-info">
                    <b-card-body class="pb-0">
                        <h4 class="mb-0">{{ data.clients.count }}</h4>
                        <p>Clients count</p>
                    </b-card-body>
                    <card-line3-chart-example class="chart-wrapper"
                                            style="height:70px;"
                                            height="70"
                                            :chartData="data.clients_weekly_data"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card no-body
                        class="bg-warning">
                    <b-card-body class="pb-0">
                        <h4 class="mb-0">{{ data.reviews.count }}</h4>
                        <p>Reviews</p>
                    </b-card-body>
                    <card-line3-chart-example class="chart-wrapper"
                                              style="height:70px;"
                                              height="70"
                                              :chartData="data.reviews_weekly_data"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card no-body
                        class="bg-success">
                    <b-card-body class="pb-0">
                        <h4 class="mb-0">{{ data.earnings.total | currency }}</h4>
                        <p>Total earnings</p>
                    </b-card-body>
                    <card-bar-chart-example class="chart-wrapper"
                                            style="height:70px;"
                                            height="70"
                                            :chartData="data.earnings_weekly_data"/>
                </b-card>
            </b-col>
        </b-row>
        <b-card>
            <b-row>
                <b-col sm="5">
                    <h4 id="traffic"
                        class="card-title mb-0">Data</h4>
                    <div class="small text-muted">
                        <template v-if="!searchDates">
                            Week {{ data.current_week }}
                        </template>
                        <template v-else>
                            {{ searchDates }}
                        </template>
                    </div>
                </b-col>
                <b-col sm="7"
                       class="d-none d-md-block">
                    <b-button-toolbar class="float-right"
                                      aria-label="Reports toolbar">
                        <b-form-radio-group class="mr-3"
                                            id="radiosBtn"
                                            buttons
                                            button-variant="outline-secondary"
                                            size="sm"
                                            v-model="selected"
                                            name="radiosBtn">
                            <b-form-radio class="mx-0"
                                          value="Week">Week
                            </b-form-radio>
                            <b-form-radio class="mx-0"
                                          value="Month">Month
                            </b-form-radio>
                            <b-form-radio class="mx-0"
                                          value="Year">Year
                            </b-form-radio>
                            <b-form-radio class="mx-0"
                                          value="Range">Date Range
                            </b-form-radio>
                        </b-form-radio-group>

                        <!--<b-btn-group v-if="selected === 'Range'">
                            <b-btn size="sm">
                                {{ form.from }}
                            </b-btn>
                            <b-btn size="sm">
                                {{ form.to }}
                            </b-btn>
                        </b-btn-group>-->
                        <b-btn v-show="selected === 'Range'"
                               variant="primary"
                               size="sm"
                               ref="rangeBtn"
                               id="exPopoverReactive1">
                            <template v-if="searchDates">
                                {{ searchDates }}
                                <icon name="caret-down"></icon>
                            </template>
                        </b-btn>
                    </b-button-toolbar>

                    <b-popover target="exPopoverReactive1"
                               triggers="click"
                               :show.sync="popoverShow"
                               placement="auto"
                               container="myContainer"
                               ref="popover">
                        <template slot="title">
                            <b-btn class="close"
                                   aria-label="Close" @click="popoverShow = false">
                                <span class="d-inline-block"
                                      aria-hidden="true">&times;</span>
                            </b-btn>
                            Select Dates
                        </template>
                        <div>
                            <b-form class="text-right"
                                    @submit.prevent="submit"
                                    @reset="reset">
                                <b-form-group id="from"
                                              label="From"
                                              label-for="from"
                                              horizontal
                                              :label-cols="4"
                                              :state="form.errors.state('from')">
                                    <b-form-input id="from"
                                                  type="date"
                                                  size="sm"
                                                  :state="form.errors.state('from')"
                                                  v-model="form.from">
                                    </b-form-input>
                                </b-form-group>

                                <b-form-group id="to"
                                              label="To"
                                              label-for="to"
                                              horizontal
                                              :label-cols="4"
                                              :state="form.errors.state('to')">
                                    <b-form-input id="to"
                                                  type="date"
                                                  size="sm"
                                                  :state="form.errors.state('to')"
                                                  v-model="form.to">
                                    </b-form-input>
                                </b-form-group>

                                <b-btn class="d-inline ml-2"
                                       type="submit"
                                       variant="primary"
                                       size="sm">
                                    <icon v-if="form.busy"
                                          name="spinner"
                                          spin></icon>
                                    Submit
                                </b-btn>
                            </b-form>
                        </div>
                    </b-popover>

                </b-col>
            </b-row>
            <main-chart-example class="chart-wrapper"
                                style="height:300px;margin-top:40px;"
                                height="300"
                                :chart-data="mainChartData"
                                v-if="mainChartData.datasets.length"
                                :yAxes="mainChartYAxes"/>
        </b-card>

        <b-card>
            <b-row class="mb-3">
                <b-col sm="5">
                    <h4 id="activities"
                        class="card-title mb-0">Activity</h4>
                </b-col>
            </b-row>

            <activities></activities>
        </b-card>

    </div>
</template>

<script>
    import CardLine1ChartExample from './dashboard/CardLine1ChartExample';
    import CardLine2ChartExample from './dashboard/CardLine2ChartExample';
    import CardLine3ChartExample from './dashboard/CardLine3ChartExample';
    import CardBarChartExample from './dashboard/CardBarChartExample';
    import MainChartExample from './dashboard/MainChartExample';
    import SocialBoxChartExample from './dashboard/SocialBoxChartExample';
    import CalloutChartExample from './dashboard/CalloutChartExample';
    import {Callout} from '../components/';
    import Activities from './dashboard/Activities';

    export default {
        name: 'dashboard',
        components: {
            Callout,
            CardLine1ChartExample,
            CardLine2ChartExample,
            CardLine3ChartExample,
            CardBarChartExample,
            MainChartExample,
            SocialBoxChartExample,
            CalloutChartExample,
            Activities,
        },
        data() {
            return {
                fbData: {},
                twData: {},
                inData: {},
                gpData: {},
                popoverShow: false,
                searchDates: null,
                mainChartData: {
                    datasets: []
                },
                mainChartYAxes: {
                    stepSize: 50,
                    max: 100,
                },
                form: new Form({
                    from: null,
                    to: null
                }),
                selected: 'Month',
                data: {
                    current_week: 0,
                    users_weekly_data: {
                        datasets: [
                            {}
                        ],
                    },
                    clients_weekly_data: {
                        datasets: [
                            {}
                        ],
                    },
                    reviews_weekly_data: {
                        datasets: [
                            {}
                        ],
                    },
                    earnings_weekly_data: {
                        datasets: [
                            {}
                        ],
                    },
                    users: {
                        count: 0,
                        data: [],
                    },
                    clients: {
                        count: 0,
                        data: [],
                    },
                    reviews: {
                        count: 0,
                        data: [],
                    },
                    earnings: {
                        total: 0,
                        data: [],
                    }
                },
            };
        },
        mounted() {
            this.setSocialData();
            this.form.from = moment().startOf('month').format('YYYY-MM-DD');
            this.form.to = moment().endOf('month').format('YYYY-MM-DD');
            this.getData();
        },
        computed: {},
        methods: {
            setSocialData() {
                let data1 = [], data2 = [], data3 = [], data4 = [],
                    chartDataLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                for (var i = 0; i <= 12; i++) {
                    data1.push(this.randomInt(50, 200));
                    data2.push(this.randomInt(60, 200));
                    data3.push(this.randomInt(70, 200));
                    data4.push(this.randomInt(80, 200));
                }
                this.fbData = {
                    labels: chartDataLabels,
                    datasets: [
                        this.generateDataSet(data1)
                    ]
                };
                this.twData = {
                    labels: chartDataLabels,
                    datasets: [
                        this.generateDataSet(data2)
                    ]
                };
                this.inData = {
                    labels: chartDataLabels,
                    datasets: [
                        this.generateDataSet(data3)
                    ]
                };
                this.gpData = {
                    labels: chartDataLabels,
                    datasets: [
                        this.generateDataSet(data4)
                    ]
                };
            },
            randomInt(min, max) {
                return Math.floor(Math.random() * (max - min + 1) + min)
            },
            generateDataSet(data, options) {
                let dataset = _.extend({
                    label: 'Label',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    borderColor: 'rgba(255,255,255,.55)',
                    data: data
                }, options);

                return dataset;
            },
            reset() {

            },
            submit() {
                this.getData();
            },
            getData() {
                this.form.startProcessing();
                this.mainChartLabels = [];
                this.mainChartDatasets = [];
                this.popoverShow = false;
                axios.get('/api/v1/dashboard', {
                    params: {
                        from: this.form.from,
                        to: this.form.to,
                    }
                })
                    .then(response => {
                        let data = response.data.data;
                        let chartDataLabels = data.labels.map(date => {
                            if (this.selected === 'Week') {
                                return moment(date).format('dddd');
                            } else {
                                return moment(date).format('MMM DD, YYYY');
                            }
                        });

                        this.data = data;

                        this.data.users_weekly_data = {
                            labels: chartDataLabels,
                            datasets: [
                                this.generateDataSet(data.users.data, {
                                    label: 'Users'
                                })
                            ]
                        };

                        this.data.clients_weekly_data = {
                            labels: chartDataLabels,
                            datasets: [
                                this.generateDataSet(data.clients.data, {
                                    label: 'Clients',
                                    backgroundColor: 'rgba(255,255,255,.3)'
                                })
                            ]
                        };

                        this.data.reviews_weekly_data = {
                            labels: chartDataLabels,
                            datasets: [
                                this.generateDataSet(data.reviews.data, {
                                    label: 'Reviews'
                                })
                            ]
                        };

                        this.data.earnings_weekly_data = {
                            labels: chartDataLabels,
                            datasets: [
                                this.generateDataSet(data.earnings.data, {
                                    label: 'Earnings',
                                    backgroundColor: 'rgba(255,255,255,.3)'
                                })
                            ]
                        };

                        let maxNum = Math.ceil(Math.max(
                            this.data.users.count,
                            this.data.clients.count,
                            this.data.reviews.count,
                            this.data.earnings.total
                        ) / 10) * 10;

                        this.mainChartYAxes = {
                            max: maxNum,
                            stepSize: Math.ceil(maxNum / 5),
                        };

                        let mainChartDatasets = [];

                        mainChartDatasets.push(
                            this.generateDataSet(data.users.data, {
                                label: 'Users',
                                borderColor: '#20a8d8'
                            }),
                            this.generateDataSet(data.clients.data, {
                                label: 'Clients',
                                borderColor: '#63c2de'
                            }),
                            this.generateDataSet(data.reviews.data, {
                                label: 'Reviews',
                                borderColor: '#ffc107'
                            }),
                            this.generateDataSet(data.earnings.data, {
                                label: 'Earnings',
                                borderColor: '#4dbd74'
                            })
                        );

                        this.mainChartData = {
                            labels: chartDataLabels,
                            datasets: mainChartDatasets
                        };

                        this.searchDates = this.form.from && this.form.to ?
                            `${this.$options.filters.date(this.form.from)} - ${this.$options.filters.date(this.form.to)}` :
                            `${this.$options.filters.date(moment().startOf('isoWeek'))} - ${this.$options.filters.date(moment().endOf('isoWeek'))}`;
                        this.form.clearFields();
                    })
                    .catch((errors) => {
                        if (errors.response) {
                            this.form.errors.set(errors.response.data.errors);
                        }
                    })
                    .finally(() => {
                        this.form.finishProcessing();
                    });
            }
        },
        watch: {
            selected(value) {
                switch (value) {
                    case 'Week':
                        this.form.from = moment().startOf('isoWeek').format('YYYY-MM-DD');
                        this.form.to = moment().endOf('isoWeek').format('YYYY-MM-DD');
                        this.getData();
                        break;
                    case 'Year':
                        this.form.from = moment().startOf('year').format('YYYY-MM-DD');
                        this.form.to = moment().endOf('year').format('YYYY-MM-DD');
                        this.getData();
                        break;
                    case 'Month':
                        this.form.from = moment().startOf('month').format('YYYY-MM-DD');
                        this.form.to = moment().endOf('month').format('YYYY-MM-DD');
                        this.getData();
                        break;
                    case 'Range':
                        this.searchDates = `${this.$options.filters.date(moment().startOf('isoWeek'))} - ${this.$options.filters.date(moment().endOf('isoWeek'))}`;
                        this.popoverShow = true;
                        break;
                }
                this.setSocialData();
            }
        }
    };
</script>
