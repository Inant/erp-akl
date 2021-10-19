@extends('theme.default')


@section('content')
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                </h4>
                                <form method="POST" action="{{ URL::to('menu/feed') }}" >
                                  @csrf
                                  <label>nomor rab</label>
                                  <input type="" class="form-control" name="set" required placeholder="rab yang akan diisi">
                                  <br>
                                  <input type="" class="form-control" name="get" required placeholder="rab yang akan copy">
                                  <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
