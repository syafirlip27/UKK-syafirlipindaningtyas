@extends('main')
@section('title', '| Dashboard')

@section('content')

<div class="row">
    @if(Auth::user()->role === 'employee')
    <div class="col-lg-12 p-3"> 
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Selamat Datang {{ Auth::user()->name }}</h4> {{-- Tetap di pinggir kiri --}}
                
                <div class="card w-100">
                    <ul class="list-group list-group-flush d-flex flex-column" style="min-height: 200px;">
                        <li class="list-group-item bg-light d-flex justify-content-center align-items-center">
                            Total Penjualan Hari Ini
                        </li>
                        <li class="list-group-item flex-grow-1 d-flex flex-column justify-content-center align-items-center" style="min-height: 100px;">
                            <b style="font-size: 2rem;">{{ $todaySalesCount }}</b>
                            <span>Data terjual hari ini:</span>
                        </li>
                        
                        <li class="list-group-item bg-light d-flex justify-content-center align-items-center">
                            Terakhir diperbarui: {{ now()->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif



    @if(Auth::user()->role === 'admin')
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex align-items-center">
                        <div>
                            <h4 class="card-title">Selamat Datang {{ Auth::user()->name }}</h4>
                        </div>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Statistik Penjualan</h4>
                    <canvas id="salesPieChart"></canvas>
                </div>
            </div>
        </div>
        
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var pieChartCanvas = document.getElementById('salesPieChart')?.getContext('2d');
        if (!pieChartCanvas) {
            console.error("Canvas dengan id 'salesPieChart' tidak ditemukan!");
            return;
        }

        var labelspieChart = @json($labelspieChart);
        var salesDatapieChart = @json($salesDatapieChart);

        new Chart(pieChartCanvas, {
            type: 'pie',
            data: {
                labels: labelspieChart, 
                   datasets: [{
                    data: salesDatapieChart, 
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        var chart = document.getElementById('salesChart')?.getContext('2d');
        if (!chart) {
            console.error("Canvas dengan id 'salesChart' tidak ditemukan!");
            return;
        }

        var labels = @json($labels); 
        var salesData = @json($salesData);

        var salesChart = new Chart(chart, {
            // type: 'bar',
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Penjualan',
                    data: salesData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });


    
</script>

@endsection
