<?php

class PDFContactForm extends csgPDFForm
{
	public function customize()
	{
		$folder = csgScript::get_argument("folder") -> value;
		$view = csgScript::get_argument("view") -> value;
		$id = csgScript::get_argument("id") -> value;
		$schema = new csgSchema($folder,$view);
		$contact = csgAsset::get_from_db($schema,$id);
		$this->multi_cells[2][1] -> text = $contact->firstname;
		$this->multi_cells[2][2] -> text = $contact->lastname;
	}
}

?>