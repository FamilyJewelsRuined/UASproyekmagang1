<main class="content">
    <div class="d-flex align-items-center gap-2 page-title">
        <i class="fa-solid fa-ship fa-xl"></i>
        <span>Permohonan Penggunaan Alur (PPA)</span>
    </div>
    <hr style="margin-bottom: 15px">

    <div class="mb-5 col-md-10 card-box">
        <p class="abu-9">Form Pengajuan Permohonan</p>
  
        <form id="ppaForm" class="row g-3" method="post" enctype="multipart/form-data" novalidate>
            <!-- Tanggal PPA -->
            <div class="form-row">
                <div class="col-md-3">
                    <label class="form-label">Tanggal PPA</label>
                    <div class="input-group">
                        <input type="text" class="form-control disabled-style" id="TANGGAL"  name="TANGGAL" value="<?php echo date('d/m/Y'); ?>" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text" style="color:#0aa2e3;"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <hr style="margin-bottom: -5px; ">
            <div class="col-12 mt-4">
                <h5>DATA INAPORTNET</h5>
            </div>
            <div class="col-md-6">
            <label class="form-label">NOMOR PKK KAPAL</label>
            <input type="text" class="form-control">
            </div>

            <div class="col-md-6">
            <label class="form-label">NOMOR PKK TONGKANG</label>
            <input type="text" class="form-control">
            </div>

            <div class="col-md-6">
            <label class="form-label">TANGGAL PKK KAPAL</label>
            <input type="text" class="form-control" disabled="">
            </div>

            <div class="col-md-6">
            <label class="form-label">TANGGAL PKK TONGKANG</label>
            <input type="text" class="form-control" disabled="">
            </div>

            <hr style="margin-bottom: -5px; ">
            <div class="col-12 mt-4">
                <h5>DATA PERMOHONAN</h5>
            </div>
            <div class="col-md-6">
                <label for="KAPAL_ID" class="form-label">Nama Kapal</label> <span class="text-danger">*</span>
                <select id="KAPAL_ID" name="KAPAL_ID" class="form-select select2-kapal" required></select>
                <input type="hidden" id="GT_KAPAL" name="GT_KAPAL">
            </div>

            <div class="col-md-6">
                <label for="TONGKANG_ID" class="form-label">Nama Tongkang</label> <span class="text-danger">*</span>
                <select id="TONGKANG_ID" name="TONGKANG_ID" class="form-control form-select select2-tongkang" required></select>
                <input type="hidden" id="GT_TONGKANG" name="GT_TONGKANG">
            </div>

            <!-- Pelabuhan -->
            <div class="col-md-6">
                <label for="PELABUHAN_ID" class="form-label">Pelabuhan Asal</label> <span class="text-danger">*</span>
                <select id="PELABUHAN_ID" name="PELABUHAN_ID" class="form-control form-select select2-tongkang" required></select>
            </div>
            <div class="col-md-6">
                <label for="PELABUHAN_TUJUAN_ID" class="form-label">Pelabuhan Tujuan</label> <span class="text-danger">*</span>
                <select id="PELABUHAN_TUJUAN_ID" name="PELABUHAN_TUJUAN_ID" class="form-control form-select select2-tongkang" required></select>
            </div>

            <!-- Jenis Pengiriman -->
            <div class="col-md-6">
                <label for="ISIDR" class="form-label">Jenis Pengiriman - Tarif</label> <span class="text-danger">*</span>
                <select id="ISIDR" name="ISIDR" class="form-select" required>
                    <option value="">Pilih Tujuan Akhir: Domestik / Ekspor</option>
                    <option value="3">DOMESTIK - IDR</option>
                    <option value="2">EKSPOR - USD</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="PELABUHAN_TUJUAN_AKHIR" class="form-label">Pelabuhan Tujuan Akhir</label> <span id="wajibTujuanAkhir" class="text-danger">*</span>
                <input style="text-transform: uppercase;" type="text" class="form-control" id="PELABUHAN_TUJUAN_AKHIR" 
                        placeholder="Isikan Nama Mother Vesel / Negara Tujuan Akhir" 
                        name="PELABUHAN_TUJUAN_AKHIR" value="">
            </div>

            <!-- Sifat Kunjungan & Tanggal Lewat -->
            <div class="col-md-6">
            <label for="SIFAT_KUNJUNGAN" class="form-label">Sifat Kunjungan</label> <span class="text-danger">*</span>
                <select id="SIFAT_KUNJUNGAN" name="SIFAT_KUNJUNGAN" class=" form-control form-select" required>
                    <option value="R">RUTIN</option>
                    <option value="I">INSIDENTIL</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="TANGGAL_LEWAT_INPUT" class="form-label">Tanggal Lewat</label> <span class="text-danger">*</span>
                <div class="input-group date">
                    <input type="text" class="form-control" id="TANGGAL_LEWAT_INPUT" name="TANGGAL_LEWAT_INPUT" autocomplete="off" required>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-calendar" style="color:#0aa2e3;"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="JAM_LEWAT_INPUT" class="form-label">Jam Lewat <span class="text-danger">*</span></label>
                <div class="input-group">
                <input type="text" id="JAM_LEWAT_INPUT" name="JAM_LEWAT_INPUT" class="form-control" placeholder="23:59"  required>
                    <span class="input-group-text">
                    <i class="fa-regular fa-clock" style="color:#0aa2e3;"></i>
                    </span>
                </div>
            </div>

            <!-- Surveyor & Draft -->
            <div class="col-md-6">
                <label for="SURVEYOR" class="form-label">Surveyor</label> <span class="text-danger">*</span>
                <select id="SURVEYOR" class="form-select select2-surveyor" required></select>
            </div>
            <div class="col-md-3">
                <label for="TANGGAL_DRAFT" class="form-label">Tanggal Draft</label>  <span class="text-danger">*</span>
                <div class="input-group date">
                    <input type="text" class="form-control" id="TANGGAL_DRAFT" name="TANGGAL_DRAFT" autocomplete="off" required>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-calendar" style="color:#0aa2e3;"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="MEAN_OF_MEANS" class="form-label">Mean of Means</label> <span class="text-danger">*</span>
                <input id="MEAN_OF_MEANS" name="MEAN_OF_MEANS" type="text" class="form-control text-end" required>
            </div>

            <hr style="margin-bottom: -5px; ">
            <!-- Penanggung Jawab -->
            <div class="col-12 mt-4">
                <h5>PENANGGUNG JAWAB PEMBAYARAN</h5>
            </div>
            <div class="col-md-6">
                <label for="CUSTOMER_ID" class="form-label">Customer</label> <span class="text-danger">*</span>
                <select id="CUSTOMER_ID" name="CUSTOMER_ID" class="form-select select2-customer" required></select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode / Account</label>
                <input id="KODE" type="text" class="form-control" disabled="">
            </div>
            <div class="col-md-3">
                <label for="TYPE_BAYAR_LABEL" class="form-label">Tipe Bayar</label>
                <input id="TYPE_BAYAR_LABEL" type="text" class="form-control" required>
                <input id="TYPE_BAYAR" name="TYPE_BAYAR" type="hidden">
            </div>
            <div class="col-md-4">
                <div>
                    <label for='NPWP' class="form-label">NPWP</label>
                    <input id="NPWP" name="NPWP" type="text" class="form-control" disabled="">
                </div>
                <!-- <div>
                    <label class="form-label mt-3">NITKU</label>
                    <input type="text" class="form-control" disabled>
                </div> -->
            </div>
            <div class="col-md-8">
                <label for="ALAMAT_NPWP" class="form-label">Alamat NPWP</label>
                <textarea id="ALAMAT_NPWP" name="ALAMAT_NPWP" class="form-control" disabled="" style="min-height:calc(1.5em + 5.85rem + calc(var(--bs-border-width) * 2))"></textarea>
            </div>
            <hr style="margin-bottom: -5px; ">

            <!-- Perhitungan Biaya -->
            <div class="col-12 mt-4">
                <h5>PERHITUNGAN BIAYA</h5>
            </div>
            <div class="col-md-4">
                <label for="JENIS_MUATAN_ID" class="form-label">Jenis Muatan</label> <span class="text-danger">*</span>
                <select id="JENIS_MUATAN_ID" name="JENIS_MUATAN_ID" class="form-select select2-muatan" required></select>
            </div>
            <div class="col-md-3">
                <label for="BERAT_MUATAN" class="form-label">Cargo Loaded</label> <span class="text-danger">*</span>
                <input id="BERAT_MUATAN" name="BERAT_MUATAN" class="form-control text-end" style="font-size:1.4rem;padding: 0rem 0.7rem 0rem 0rem;" required>
            </div>
            <div class="col-md-5">
                <label id="label-tarif" for="TARIF_USD" class="form-label">Tarif USD</label>
                <input id="TARIF_USD" name="TARIF_USD" class="form-control text-end disabled-style" required readonly>
            </div>
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <label for="TRXKPJK" class="form-label">Kurs Transaksi</label>
                <input id="TRXKPJK" name="TRXKPJK" type="text" class="form-control text-end disabled-style" required readonly >
            </div>
            <div class="col-md-7"> </div>
            <div class="col-md-5">
                <label for="NILAI_USD" class="form-label">Nilai PPA</label>
                <input id="NILAI_USD" name="NILAI_USD" type="text" class="form-control text-end disabled-style" required readonly >
            </div>
            <div class="col-md-5"> </div>
            <div class="col-md-2">
                <label for='PPN_PERSEN' class="form-label">PPn Persen</label>
                <input id='PPN_PERSEN' name='PPN_PERSEN' class="form-control text-end disabled-style" required readonly >
            </div>
            <div class="col-md-5">
                <label for="PPN" class="form-label">Nilai PPn (IDR)</label>
                <input id="PPN" name="PPN" class="form-control text-end disabled-style" required readonly >
            </div>
            <div class="col-md-7"> </div>
            <div class="col-md-5">
                <label for="TOTAL_USD" class="form-label">Jumlah (IDR)</label>
                <input id="TOTAL_USD" name="TOTAL_USD" class="form-control text-end disabled-style" style=" font-size:1.4rem;padding: 0rem 0.7rem 0rem 0rem;"required readonly >
            </div>

            <!-- Keterangan -->
            <div class="col-12">
                <label for="KETERANGAN" class="form-label">Keterangan</label>
                <textarea id="KETERANGAN" name="KETERANGAN" class="form-control"></textarea>
            </div>

            <hr>
            <!-- Dokumen Pelengkap -->
            <div class="col-12 mt-4">
                <h5>DOKUMEN LAMPIRAN</h5>
            </div>
            <div class="col-md-4">
                <label for="DRAFT" class="form-label">Draft Survey (PDF)</label>
                <input id="DRAFT" name="DRAFT" type="file" class="form-control" name="draft_survey" accept="application/pdf" required>
            </div>
            <div class="col-md-4">
                <label for="SLIP" class="form-label">Slip Setoran / SP (PDF)</label>
                <input id="SLIP" name="SLIP" type="file" class="form-control" name="slip_setoran" accept="application/pdf" required>
            </div>
            <div class="col-md-4">
                <label for="SKAB" class="form-label">S K A B  (PDF)</label>
                <input id="SKAB" name="SKAB" type="file" class="form-control" name="surat_asal" accept="application/pdf" required>
            </div>

            <!-- Tombol -->
            <div class="col-12 mt-3">
            <button type="button" id="btnSimpan" class="btn btn-primary">Simpan PPA</button>
            <button type="button" id="btnReset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</main>

<!-- Toast Container (pojok kanan atas) -->
<div class="toast-container position-fixed top-c40 end-c20 p-3" style="z-index: 1055">
  <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-msg">
        <!-- Pesan dari JS -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<?php $this->load->view('permohonan/view_permohonan_js'); ?>