@extends('theme.default')

@section('css-content')
    <link href="{!! asset('public/theme/dist/css/gallery.css') !!}" rel="stylesheet">
    <script src="{!! asset('public/theme/dist/js/gallery.js') !!}"></script>
@endsection

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Gallery</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Data Gallery</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
<div class="container p-3">
        <div class="text-right">
            <a href="{{ URL::to('menu/gambar') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new gallery</button></a>
        </div>
        <div class="fg-gallery">

            </div>

            <div class="fg-gallery ns">
                @foreach ($data as $d)
                    <img src="{{ asset('public/upload/photo/'.$d->filename) }}" alt="">
                @endforeach
            </div>
</div>


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

@section('js-content')
    <script>
        var a = new FgGallery('.fg-gallery', {
            cols: 4,
            style: {
                border: '10px solid #fff',
                height: '180px',
                borderRadius: '5px',
                boxShadow: '0 2px 10px -5px #858585',
            }
        })

        var a = new FgGallery('.ns', {
            cols: 3,
            style: {
                border: '0 solid #fff',
                height: '240px',
                borderRadius: '5px',
            }
        })
    </script>
@endsection
@endsection
