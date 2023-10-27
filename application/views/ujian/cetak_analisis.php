<?php
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    public function Header() {
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(13);
        $this->Cell(0, 15, 'Examination Results', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Examination Results');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$mulai = date('l, j F Y', strtotime($ujian->tgl_mulai));
$selesai = date('l, j F Y', strtotime($ujian->terlambat));


// create some HTML content
$html = <<<EOD
<p>
Online Examination System in PHP using CodeIgniter Framework. </br>
All the detailed information are provided below with Highest, Lowest and Average Score of the student!
</p>
EOD;

$html .= <<<EOD
<br><br><br>
<table border="1" style="border-collapse:collapse">
    <thead>
        <tr align="center">
            <th width="5%">No</th>
            <th width="35%"><b>Tingkat Kesukaran</b></th>
            <th width="15%"><b>Tingkat Daya Pembeda</b></th>
            <th width="25%"><b>Validitas</b></th>
            <th width="10%"><b>Kriteria</b></th>
        </tr>        
    </thead>
    <tbody>
EOD;

$no = 1;
foreach($jml_benar as $row) {

    $new_row = intval($row);

    $new_row /= count($jml_soal);

    $dayaPembeda = (intval($kelas_atas[$no-1]) - intval($kelas_bawah[$no-1])) / $jml_atas_bawah;

    $kategoriKesukaran = '';
    if ($row < 0.16){
        $kategoriKesukaran = 'Sangat Sukar';
    }else if ($row < 0.31){
        $kategoriKesukaran = 'Sukar';
    }else if ($row < 0.71){
        $kategoriKesukaran = 'Sedang';
    }else if ($row < 0.86){
        $kategoriKesukaran = 'Mudah';
    }else {
        $kategoriKesukaran = 'Sangat Mudah';
    }

    $kategoriPembeda = '';
    if ($dayaPembeda <= 0){
        $kategoriPembeda = 'Rendah Sekali';
    }else if ($dayaPembeda < 0.21){
        $kategoriPembeda = 'Rendah';
    }else if ($dayaPembeda < 0.41){
        $kategoriPembeda = 'Sedang';
    }else if ($dayaPembeda < 0.71){
        $kategoriPembeda = 'Tinggi';
    }else {
        $kategoriPembeda = 'Tinggi Sekali';
    }

    $html .= <<<EOD
    <tr>
        <td align="center" width="5%">{$no}</td>
        <td width="35%">{$new_row} ($kategoriKesukaran)</td>
        <td width="15%">{$dayaPembeda}</td>
        <td width="25%">{$new_row}</td>
        <td width="10%">{$new_row}</td>
    </tr>
EOD;
    $no++;
}

$html .= <<<EOD
    </tbody>
</table>
EOD;

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);
// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tes.pdf', 'I');
