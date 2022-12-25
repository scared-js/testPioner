<script>
    import {Line} from 'vue-chartjs'
    export default {
        extends: Line,
        props: {
            chartdata: {
                type: Object,
                default: null
            },
            options: {
                type: Object,
                default: null
            }
        },
        watch: {
            chartdata: {
                handler: function (val, oldVal) {
                    this.renderLineChart();
                },
                deep: true
            }
        },
        mounted () {
            this.renderLineChart();
        },
        methods: {
            renderLineChart: function() {
                this.renderChart(this.chartdata, {
                    ...this.options,
                });
            }
        }
    }
</script>
