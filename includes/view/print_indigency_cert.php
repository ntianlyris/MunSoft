<?php
ob_start();
//============================================================+
// File name   : example_009.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 009 for TCPDF class
//               Test Image
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Test Image
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');
require_once('../class/Indigent.php');

$Indigent = new Indigent();

$ind_cert_id = $_GET['indcertid'];
$ind_cert_name = "";
$ind_cert_age = "";
$ind_cert_civil = "";
$ind_cert_brgy = "";
$ind_cert_rqstdby = "";
$ind_cert_assistance = "";
$ind_cert_day = "";
$ind_cert_mo = "";
$ind_cert_yr = "";
$ind_cert_refnum = "";

$ind_cert_details = $Indigent->GetIndigencyCertificateDetails($ind_cert_id);

$ind_cert_name = $ind_cert_details['ind_cert_name'];
$ind_cert_age = $ind_cert_details['ind_cert_age'];
$ind_cert_civil = $ind_cert_details['ind_cert_civil'];
$ind_cert_brgy = $ind_cert_details['ind_cert_brgy'];
$ind_cert_rqstdby = $ind_cert_details['request_of'];
$ind_cert_assistance = $ind_cert_details['assist_type'];
$ind_cert_day = $ind_cert_details['ind_cert_day'];
$ind_cert_mo = $ind_cert_details['ind_cert_mo'];
$ind_cert_yr = $ind_cert_details['ind_cert_yr'];
$ind_cert_refnum = $ind_cert_details['ind_cert_refnum'];

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'FOLIO', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MSWDO');
$pdf->SetTitle('Certificate of Indigency');
$pdf->SetSubject('Certificate');
$pdf->SetKeywords('Certificate, PDF, indigency, indigent, cert');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

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

// -------------------------------------------------------------------

// add a page
$pdf->AddPage();

// set JPEG quality
$pdf->setJPEGQuality(75);

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Stretching, position and alignment example
//$html ='<img src="../images/polanco_logo1.png" alt="polanco_logo" width="25" height="25" border="0" />';
//$pdf->writeHTML($html, true, false, true, false, '');
$pdf->SetXY(40, 15);
$pdf->Image('../images/polanco_logo.jpg', '', '', 26, 26, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->SetXY(150, 16);
$pdf->Image('../images/DSWD-Logo.jpg', '', '', 25, 23, '', '', '', false, 300, '', false, false, 0, false, false, false);

// -------------------------------------------------------------------

$pdf->SetFont('helvetica', '', 12);
$pdf->SetXY(15, 20);
$pdf->Write(0, 'Republic of the Philippines', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Province of Zamboanga del Norte', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Municipality of Polanco', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(15, 45);
$pdf->Write(0, ' OFFICE OF THE MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('helvetica', 'B', 28);
$pdf->SetXY(15, 65);
$pdf->Write(0, 'CERTIFICATE OF INDIGENCY', '', 0, 'C', true, 0, false, false, 0);


// create some HTML content to justify
$html1 = '<p style="text-align:justify; text-indent: 25px;">
            <strong>THIS IS TO CERTIFY</strong> that <strong><u>'.$ind_cert_name.'</u></strong>, <u>'.$ind_cert_age.'</u> years old,
            <u>'.$ind_cert_civil.'</u> and a resident of Barangay <u>'.$ind_cert_brgy.'</u>, Polanco, Zamboanga del Norte is considered
            indigent based on our evaluation and assessment. The family is financially unstable that could hardly meet
            their urgent needs.
        </p>';

$html2 = '<p style="text-align:justify; text-indent: 25px;">
        This ceritification is being issued upon the request of <strong><u>'.$ind_cert_rqstdby.'</u></strong> for <u>'.$ind_cert_assistance.'</u>.
    </p>';

$html3 = '<p style="text-align:justify; text-indent: 25px;">
    Given this <u>'.$ind_cert_day.'</u> day of <u>'.$ind_cert_mo.'</u>, <u>'.$ind_cert_yr.'</u> at Polanco, Zamboanga del Norte, Philippines.
</p>';

$html4 = '<div style="border-bottom:1px solid;"></div>';


$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 90);
$pdf->Write(0, 'TO WHOM IT MAY CONCERN:', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 12);
$pdf->SetXY(25, 105);
$pdf->writeHTML($html1, true, 0, true, true);
$pdf->SetXY(25, 135);
$pdf->writeHTML($html2, true, 0, true, true);
$pdf->SetXY(25, 155);
$pdf->writeHTML($html3, true, 0, true, true);

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(15, 200);
$pdf->Write(0, 'CATHERINE M. CAULAWON', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('helvetica', '', 14);
$pdf->SetXY(15, 206);
$pdf->Write(0, 'MSWDO', '', 0, 'C', true, 0, false, false, 0);
$line_sign = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0);
$line_head = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 69, 0));
$pdf->Line(72, 206, 139, 206, $line_sign);
$pdf->Line(24, 55, 188, 55, $line_head);

ob_end_clean();
//Close and output PDF document
$pdf->Output('certificate_indigency_'.$ind_cert_refnum.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
