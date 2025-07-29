@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3 bg-primary text-white">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Form Tambah</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formData">
                                    @csrf
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="nama" name="nama" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_kelamin" id="jenis_kelamin"
                                                class="form-control select-jenis-kelamin">
                                                <option value=""></option>
                                                <option value="1">Laki - laki</option>
                                                <option value="2">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="date" id="tgl_lahir" name="tgl_lahir" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Foto</label>
                                        <div class="col-sm-3">
                                            <input type="file" id="foto" name="foto" accept=".jpg, .jpeg, .png">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                                aria-hidden="true"></span>
                                            <span class="button-text">Simpan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-jenis-kelamin').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Kelamin",
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('crud-page.store') }}';
            let method = 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    sessionStorage.setItem('success', 'Data berhasil ditambahkan!');
                    window.location.href = "{{ route('crud-page.index') }}";
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });
    </script>
@endpush
