@extends('layouts.main')
@section('title') Statistik Omzet @endsection
@section('css')
<link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Statistik Omzet per Cabang</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Periode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="startDate" placeholder="Dari Bulan">
                            <input type="text" class="form-control" id="endDate" placeholder="Sampai Bulan">
                            <button class="btn btn-primary" id="filterBtn">Filter</button>
                        </div>
                    </div>
                </div>
                <div id="omzetChart"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set nilai default untuk filter tanggal
        const defaultStartDate = moment().startOf('year').format('MMM YYYY');
        const defaultEndDate = moment().format('MMM YYYY');

        // Inisialisasi date picker dengan nilai default
        flatpickr("#startDate", {
            plugins: [],
            monthOnly: true,
            dateFormat: "M Y",
            defaultDate: defaultStartDate
        });

        flatpickr("#endDate", {
            plugins: [],
            monthOnly: true,
            dateFormat: "M Y",
            defaultDate: defaultEndDate
        });

        // Update nilai input
        $('#startDate').val(defaultStartDate);
        $('#endDate').val(defaultEndDate);

        // Fungsi untuk memuat chart
        function loadChart(startDate, endDate) {
            $.ajax({
                url: "{{ route('omzet-statistic.chart-data') }}",
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    Highcharts.chart('omzetChart', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Statistik Omzet per Cabang'
                        },
                        xAxis: {
                            categories: response.categories
                        },
                        yAxis: {
                            title: {
                                text: 'Total Omzet'
                            }
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal'
                            }
                        },
                        series: response.series
                    });
                }
            });
        }

        // Event handler untuk tombol filter
        $('#filterBtn').click(function() {
            loadChart($('#startDate').val(), $('#endDate').val());
        });

        // Load chart pertama kali
        loadChart(
            moment().startOf('year').format('MMM YYYY'),
            moment().format('MMM YYYY')
        );
    });
</script>
@endsection