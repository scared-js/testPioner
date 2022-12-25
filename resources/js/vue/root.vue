<template>
    <div>
        <div class="pre-loader" v-if="data.isFetching"></div>
        <div class="container">
            <div class="wrapper">
                <div class="form-group col-12">
                    <label>От</label>
                    <input type="date" v-model="data.date_in" class="form-control">
                </div>
                <div class="form-group col-12">
                    <label>До</label>
                    <input type="date" v-model="data.date_out" :max="Date.now()" class="form-control">
                </div>
                <div class="form-group col-12 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger" v-on:click="update_data">
                        Загрузить данные
                    </button>
                </div>
            </div>
            <div class="wrapper" v-if="data.rows.length">
                <line-chart :chartdata="chart_data"
                            :options="chart_options">
                </line-chart>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import axios from 'axios'
    import VueSweetalert2 from 'vue-sweetalert2';
    import 'sweetalert2/dist/sweetalert2.min.css';
    Vue.use(VueSweetalert2);
    import LineChart from './line'


    export default Vue.extend({
        components: {
            LineChart
        },
        computed:{
            chart_options(){
                return {
                    responsive: true,
                    maintainAspectRatio: false
                }
            },
            labels(){
              return this.data.rows.map((item)=>{
                  return item.date
              })
            },
            value(){
                return this.data.rows.map((item)=>{
                    return item.data
                })
            },
            chart_data(){
                return {
                    labels: this.labels,
                    datasets: [
                        {
                            label: 'Data One',
                            backgroundColor: '#f87979',
                            data: this.value
                        }
                    ]
                }
            }
        },
        data: () => {
            return {
                data: {
                    isFetching: false,
                    date_in: null,
                    date_out: null,
                    rows: [],
                }
            }
        },
        methods: {
            update_data(){
                let $this = this;
                let $options = {
                    url: '/rates/search',
                    data: {
                        'date_in': $this.data.date_in,
                        'date_out': $this.data.date_out,
                    },
                    success: ($response) => {
                        $this.data.rows = $response.data.rows;
                    },
                };
                $this.$root.request($options);
            },
            request($options) {
                let $this = this;
                $this.data.isFetching = true;
                if (typeof $options.success !== 'function') {
                    $options.success = () => {
                    };
                }
                if (typeof $options.error !== 'function') {
                    $options.error = () => {
                    };
                }
                axios({
                    method: 'post',
                    url: $options.url,
                    data: $options.data,
                }).then((response) => {
                    if (response.data.success) {
                        $this.data.isFetching = false;
                        $options.success(response);
                    } else {
                        let $text = '';
                        if (response.data.message) {
                            $text = response.data.message;
                        } else {
                            $text = 'Ошибка на сервере.';
                        }
                        if ($options.showError !== false) {
                            $this.$swal({
                                type: 'error',
                                showConfirmButton: false,
                                showCloseButton: false,
                                html: $text.replaceAll('\n', '<br>'),
                                onAfterClose: () => {
                                    $this.data.isFetching = false;
                                    if ($options.error) {
                                        $options.error();
                                    }
                                },
                            });
                        } else {
                            $this.data.isFetching = false;
                            if ($options.error) {
                                $options.error();
                            }
                        }
                    }
                }).catch(function ($error) {
                    if ($error.response) {
                        console.log($error.response.data);
                        console.log($error.response.status);
                        console.log($error.response.headers);
                    }
                    if ($options.showError !== false) {
                        $this.$swal({
                            type: 'error',
                            showConfirmButton: false,
                            showCancelButton: false,
                            text: 'Ошибка на сервере.',
                            onAfterClose: () => {
                                $this.data.isFetching = false;
                                if ($options.error) {
                                    $options.error($error);
                                }
                            },
                        });
                    } else {
                        $this.data.isFetching = false;
                        if ($options.error) {
                            $options.error($error);
                        }
                    }
                    console.log($error);
                });
            },
        }
    });
</script>
<style lang="scss">
    @import '~bootstrap/scss/bootstrap.scss';

    .pre-loader {
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1051;
        font-size: 2rem;
        background-color: rgba(128, 128, 128, 0.1);
        background-clip: padding-box;
    }

    .container {
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;

        .wrapper {
            width: 1028px;
        }
    }
</style>
