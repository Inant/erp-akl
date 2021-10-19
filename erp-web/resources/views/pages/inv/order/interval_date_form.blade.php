<div class="container-fluid" style="min-height:auto">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pilih Tanggal</h4>
                    <form action="" method="get">
                    <div class="row">
                        <div class="col-6">
                            <label for="">Dari Tanggal</label>
                            <input type="date" value="{{isset($_GET['dari']) ? $_GET['dari'] : ''}}" name="dari" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="">Sampai Tanggal</label>
                            <input type="date" value="{{isset($_GET['sampai']) ? $_GET['sampai'] : ''}}" name="sampai" class="form-control">
                        </div>
                        <div class="col-6 mt-3">
                            <button class="btn btn-success">Tampilkan Data</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
