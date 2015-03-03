<?php

/*
 * usage
 * -----
 * calling the script with
 * http://<your-server>/index.php?csg=export_to_pdf&template='pdfform'&folder=&view=&id=
 * where pdfform is a custom class creating a pdf document 
 *
 * configuration
 * -------------
 * please adjust the following variables to your needs:
 * fpdf: FPDF_FONTPATHm, path_to_fpdf.php
 * $pdf_template_schema: the folder where the fpdf templates are stored
 * $pdf_multi_cells: the folder where the fpdf multi_cells are stored
 *
 */
$fpdf = 'ext/ext/fpdf17/fpdf.php';
$fpdf_font = 'ext/ext/fpdf17/font/';

// includes fpdf engine
// adjust to the correct path
if (!defined('FPDF_FONTPATH'))
	{
		define('FPDF_FONTPATH',csg::get_sgs_directory() . $fpdf_font);
	}

require (csg::get_sgs_directory() . $fpdf);


// custom schema where the pdf template is stored in the database
$pdf_template_schema = new csgSchema($folder_id = 35501, $view_name = "csg");

// custom schema where the multi cell templates are stored in the database
// make the folder accessible in read modus for the users
$pdf_multi_cell_schema = new csgSchema($folder_id = 29401, $view_name = "csg");

// get the requested pdf template name
if (!csgScript::exists_argument("template")) {
   exit("missing template request");
} else {
	$pdf_template_name = csgScript::get_argument("template") -> value;

}


// gets the asset representing the pdf template
$pdf_template_folder = $pdf_template_schema -> get_folder();


$pdf_template_folder -> view -> add_filter (
		new csgFilter('name', csgFilter::equal, $pdf_template_name));

$pdf_template_asset = $pdf_template_folder -> assets;

if (count($pdf_template_asset)>0) {
 	$pdf_template_asset = current($pdf_template_asset);
} else {
	exit("missing pdf template");
}


// creates and processes a new pdf document
$pdf_document = new $pdf_template_name(
		$form = $pdf_template_asset,
		$multi_cells = $pdf_multi_cell_schema);
$pdf_document -> customize();
ob_end_clean();
$pdf_document -> output('document.pdf','D');
exit;
?>
