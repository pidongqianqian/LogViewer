@extends('log-viewer::_template.master')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <canvas id="stats-doughnut-chart" height="300"></canvas>
        </div>
        <div class="col-md-6" style="padding-top: 20px">
            <div class="list-group">
                <a href="#" class="list-group-item active">
                    自定义log目录
                </a>
                @foreach($folders as $key => $folder)
                    <a href="/log-viewer/logs?f={{ base64_encode($folder) }}" class="list-group-item">
                        {{ $key  }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
