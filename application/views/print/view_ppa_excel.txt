<link href="https://fonts.googleapis.com/css2?family=Barlow&display=swap" rel="stylesheet">
<style>
    @import url('https://fonts.cdnfonts.com/css/calibri-light');
    
    html {
    font-size: 14px; /*ukuran teks  awal*/
}

body {
    font-family: 'Barlow', sans-serif;
    font-size: 1rem;
}

.header {
    font-size: 2rem; 
    font-weight: bold;
    vertical-align:top;
}

.header2 {
    font-size: 1rem; 
    vertical-align:top;
}

tbody {
    font-size: 0.85rem; 
}

.besar {
    font-size: 2rem;
    text-align: center;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse; 
}

thead th {
    padding: 0.5rem;
    border-bottom: 1px solid black;

}

tbody td {
    padding: 0.4rem;
    border-bottom: 1px solid black;

}

</style>

<?php 
if($excel==1){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"ppa.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");
}
?>

<table style="width:100%; border:none;">
    <tr>
<!-- bagian kiri info perusahaan-->

            <div class="header" colspan="23">PT AMBANG BARITO NUSAPERSADA</div>
            <div class="header2" colspan="23">Jl. Yos Sudarso No. 6 RT.34</div>
            <div class="header2" colspan="23">Banjarmasin Kalimantan Selatan - 70119</div>

    </tr>
    <tr>
    <td colspan="2" class="besar" style="border:none;">Daftar Permohonan PPA</td>
</tr>

    <tr>
        <td colspan="2" style="text-align: center; font-size: 1rem; margin-top: 10px;">
            <?php 
            if(isset($tw) && isset($tk) && $tw && $tk) {
                $tgl_awal = date('d/m/Y', strtotime($tw));
                $tgl_akhir = date('d/m/Y', strtotime($tk));
                echo "Periode: " . $tgl_awal . " s/d " . $tgl_akhir;
            } elseif(isset($tw) && $tw) {
                $tgl_awal = date('d/m/Y', strtotime($tw));
                echo "Periode: Dari " . $tgl_awal;
            } elseif(isset($tk) && $tk) {
                $tgl_akhir = date('d/m/Y', strtotime($tk));
                echo "Periode: Sampai " . $tgl_akhir;
            } else {
                echo "Periode: Semua Data";
            }
            ?>
        </td>
    </tr>
</table>


<table>
    <thead>
        <tr style="border-top: 1px solid black;">
            <th>NO</th>
            <th>NO PPA</th>
            <th>TYPE BAYAR</th>
            <th>TANGGAL</th>
            <th>NAMA CUSTOMER</th>
            <th>NAMA AGEN</th>
            <th>NAMA KAPAL</th>
            <th>NAMA TONGKANG</th>
            <th>JENIS MUATAN</th>
            <th>BERAT MUATAN</th>
            <th>ISIDR</th>
            <th>TRXKPJK</th>
            <th>JENIS KIRIM</th>
            <th>TIPE BAYAR</th>
            <th>TARIF USD</th>
            <th>NILAI USD</th>
            <th>PPN</th>
            <th>BY ADMIN</th>
            <th>TOTAL USD</th>
            <th>PELABUHAN ASAL</th>
            <th>PELABUHAN TUJUAN</th>
            
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
    <?php
$no = 0;

// inisialisasi penjumlahan
$berat_muatan = 0;
$total_nilai_usd = 0;
$total_usd       = 0;
$total_admin     = 0;
$total_ppn       = 0;

foreach($ppa as $ppaitem) {
    $no++;

    // menentukan nanti siapa yg ada di kolom "Nama Customer"
    if ($this->session->userdata('group') == '101') { 
        // kalau sesi sebagai agen, "nama customer" disi dgn nama agen
        $namaCustomer = $ppaitem['NAMA_AGEN'];
    } else { 
       
        $namaCustomer = $ppaitem['NAMA_CUSTOMER']; // dan sebaliknya
    }

    if ($this->session->userdata('group') == '101') { 
        // klo sesi sebagai agen, "nama agen" diisi lwn "nama customer"
        $namaAgen = $ppaitem['NAMA_CUSTOMER'];
    } else { 
       
        $namaAgen = $ppaitem['NAMA_AGEN']; //dan sebaliknya
    }

// akumulasi total
$berat_muatan += $ppaitem['BERAT_MUATAN'];
$total_nilai_usd += $ppaitem['NILAI_USD'];
$total_usd       += $ppaitem['TOTAL_USD'];
$total_admin     += $ppaitem['BY_ADMIN'];
$total_ppn       += $ppaitem['PPN'];

    echo "<tr>";
    echo "<td>" . $no . "</td>";
    echo "<td>" . $ppaitem['NO_PPA'] . "</td>";
    echo "<td>" . $ppaitem['TYPE_BAYAR'] . "</td>";
    echo "<td>" . $ppaitem['TANGGAL'] . "</td>";
    echo "<td>" . $namaCustomer . "</td>"; // sesuai kondisi
    echo "<td>" . $namaAgen . "</td>"; //nama agen sesuai kondisi
    echo "<td>" . $ppaitem['NAMA_KAPAL'] . "</td>";
    echo "<td>" . $ppaitem['NAMA_TONGKANG'] . "</td>";
    echo "<td>" . $ppaitem['JENIS_MUATAN'] . "</td>";
    echo "<td>" . number_format($ppaitem['BERAT_MUATAN'], 3, '.', ',') . "</td>";
    echo "<td>" . $ppaitem['ISIDR'] . "</td>";
    echo "<td>" . ($ppaitem['TRXKPJK']) . "</td>";
    echo "<td>" . $ppaitem['JENISKIRIM'] . "</td>";
    echo "<td>" . $ppaitem['TYPEBAYAR'] . "</td>";
    echo "<td>" . number_format($ppaitem['TARIF_USD'], 0, '.', ',') . "</td>";
    echo "<td>" . number_format($ppaitem['NILAI_USD'], 0, '.', ',') . "</td>";
    echo "<td>" . $ppaitem['PPN'] . "</td>";
    echo "<td>" . $ppaitem['BY_ADMIN'] . "</td>";
    echo "<td>" . number_format($ppaitem['TOTAL_USD'], 0, '.', ',') . "</td>";
    echo "<td>" . $ppaitem['PELABUHAN_ASAL'] . "</td>";
    echo "<td>" . $ppaitem['PELABUHAN_TUJUAN'] . " - " . $ppaitem['PELABUHAN_TUJUAN_AKHIR'] . "</td>";
    //echo "<td>" . $ppaitem['PELABUHAN_TUJUAN_AKHIR'] . "</td>";
    echo "<td>" . $ppaitem['KETERANGAN'] . "</td>";
    echo "</tr>";
}

// baris total
echo "<tr style='font-weight:bold;'>";
echo "<td colspan='9' style='text-align:right;'>TOTAL</td>"; // sejajar ke BERAT MUATAN
echo "<td>" . number_format($berat_muatan, 0, '.', ',') . "</td>"; // kolom 10: BERAT MUATAN
// kolom 11-14 dikosongkan
echo "<td colspan='4'></td>";
// kolom 15  dikosongkan
echo "<td></td>";
//  total yang lainnya
echo "<td>" . number_format($total_nilai_usd, 0, '.', ',') . "</td>"; // NILAI USD
echo "<td>" . number_format($total_ppn, 0, '.', ',') . "</td>";       // PPN
echo "<td>" . number_format($total_admin, 0, '.', ',') . "</td>";     // BY ADMIN
echo "<td>" . number_format($total_usd, 0, '.', ',') . "</td>";       // TOTAL USD
// kolom 20-22 dikosongkan
echo "<td colspan='3'></td>";
echo "</tr>";


?>
    </tbody>
</table>

