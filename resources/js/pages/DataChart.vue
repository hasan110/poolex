<template>
  <div>

    <div class="container">
        <div class="row">
            <div class="col-4">

                <select @change="getData()" v-model="filters.period" class="form-control">
                    <option :value="1">روزانه</option>
                    <option :value="2">هفتگی</option>
                    <option :value="3">ماهانه</option>
                </select>

            </div>
            <div class="col-4">

                <date-picker @change="getData()" custom-input=".custom-input-date" v-model="filters.date" displayFormat="jYYYY-jMM-jDD" locale="fa" format="jYYYY-jMM-jDD" />
                <input
                type="text"
                class="custom-input-date form-control"
                placeholder="انتخاب تاریخ"
                />
            </div>
        </div>

        <div class="row" v-if="chartDataLoaded">
            <div class="col-12" style="height:75vh;background:#fff">
                <apexchart type="bar" height="100%" width="100%" :options="chart_data.chartOptions" :series="chart_data.series"></apexchart>
            </div>
        </div>
    </div>

  </div>
</template>
<script>
export default {
  name:'DataChart',
  data: () => ({
    chartDataLoaded:false,
    filters : {
        plan:null,
        section:null,
        period:1,
    },
    chart_data : {
        series: [{
            name: '',
            data: [0 , 0, 0,0 , 0, 0,0 , 0, 0]
        }],
        chartOptions: {
            chart: {
            type: 'bar',
            height: 350
            },
            plotOptions: {
            bar: {
                borderRadius: 1,
                horizontal: false,
            }
            },
            dataLabels: {
            enabled: false
            },
            xaxis: {
            categories: ['','','','','','','','','',''],
            }
        },
    },
  }),
  methods:{
    getData(){
      this.chartDataLoaded = false
      this.SPIN_LOADING(1)
      this.$axios.post(`get_chart_data` , this.filters)
      .then(res => {
        const d = res.data.data

        this.chart_data.series[0].name = d.title;
        this.chart_data.series[0].data = d.data;
        this.chart_data.chartOptions.xaxis.categories = d.labels;

        this.chartDataLoaded = true
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    }
  },
  mounted(){
    this.getData()
  },
  beforeMount(){
    this.filters.section = this.$route.params.section
  }
}
</script>
<style>
</style>
