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
                                    <h3 class="font-weight-bold text-lg">Form Edit</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formData">
                                    @csrf
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="nama" name="nama" class="form-control"
                                                value="{{ $data->nama }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_kelamin" id="jenis_kelamin"
                                                class="form-control select-jenis-kelamin">
                                                <option value=""></option>
                                                <option value="1" {{ $data->jenis_kelamin == 1 ? 'selected' : '' }}>
                                                    Laki - laki</option>
                                                <option value="2" {{ $data->jenis_kelamin == 2 ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="date" id="tgl_lahir" name="tgl_lahir" class="form-control"
                                                value="{{ $data->tgl_lahir }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Foto</label>
                                        <div class="col-sm-3">
                                            <input type="file" id="foto" name="foto" accept=".jpg, .jpeg, .png">
                                            @if (!empty($data->foto))
                                                <small class="text-primary lihat-foto"
                                                    data-src="{{ asset('assets/foto/' . $data->foto) }}"
                                                    style="cursor:pointer;" data-title="Foto">
                                                    Lihat Foto
                                                </small>
                                            @endif
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

    <div class="modal fade" id="modalFoto" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalFotoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFotoLabel">Foto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modalFotoContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
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
        
        $(document).on('click', '.lihat-foto', function() {
            let src = $(this).data('src');
            let title = $(this).data('title') || 'Foto';

            $('#modalFotoLabel').text(title);

            let ext = src.split('.').pop().toLowerCase();

            let content = '';
            if (ext === 'pdf') {
                content = `<iframe src="${src}" width="100%" height="600px"></iframe>`;
            } else {
                content = `<img src="${src}" alt="Lampiran" class="img-fluid rounded shadow">`;
            }

            $('#modalFotoContent').html(content);
            $('#modalFoto').modal('show');
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

            let id = '{{ $data->id }}';
            let url = '{{ route('crud-page.update', ['crud_page' => ':id']) }}'.replace(':id', id);
            let method = 'PUT';

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
                    sessionStorage.setItem('success', 'Data berhasil diupdate!');
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
