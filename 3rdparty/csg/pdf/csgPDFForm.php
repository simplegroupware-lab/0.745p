<?php

/*
 *  define('FPDF_FONTPATH','../ext/ext/fpdf17/font/');
 *  include("../ext/ext/fpdf17/fpdf.php");

 *	http://<your-server>/index.php?csg=pdf&report=asset&folder=&view=&id=
 */
 
class
	csgPDFForm extends FPDF

{

// static features


// creation

	// $form comes from csgAsset::get_from_db(new csgSchema($folder,$view),$id)
	public function __construct(csgAsset $form, csgSchema $multi_cells)
	{
		$this -> form = $form;

		$this -> build_multi_cell_array($multi_cells);

		parent::__construct(
				$this -> form -> orientation,
				$this -> form -> unit,
				$this -> form -> size);
				
		$this -> AliasNbPages();
		
		$this -> SetMargins(
				$this -> form -> leftmargin,
				$this -> form -> topmargin,
				$this -> form -> rightmargin);
				
		$this -> AddPage();
	}
	
	
// features

	// check fpdf.org output method for information of possible parameter values
	public function output ($name = null, $destination = null)
	{
		$this -> write_cells();
		parent::output($name, $destination);
	}

	// needs refinement in descendants
	public function customize()
	{
	}

	
// Implementation

	protected $form;
	protected $multi_cells;
	
	protected function build_multi_cell_array(csgSchema $multi_cells)
	{
		$cells = $multi_cells -> get_folder();

		$cells -> view -> add_filter(new csgFilter('fpdf',csgFilter::equal,$this -> form -> id));
		$cells = $cells -> assets;
		$this -> multi_cells = array();
		foreach ($cells as $cell)
		{
			$this -> multi_cells[ $cell -> row ][ $cell -> col ] = $cell;
		}

		// sort multicells
		ksort($this -> multi_cells, $sort_flags = SORT_NUMERIC);
		foreach ($this -> multi_cells as &$row)
		{
			ksort($row, $sort_flags = SORT_NUMERIC);
		}

	}

	// writes the multi cell elements to the pdf
	protected function write_cells()
	{
		foreach ($this->multi_cells as $row)
		{
			
			$h = $this -> row_height($row);

			// add new page if not space enough for row
			if (($this -> GetY() + $h) >
				($this -> PageBreakTrigger))
			{
				$this -> AddPage( $this -> CurOrientation );
			}
			
			
			// draw the cells of the row
			foreach ($row as $cell)
			{
				// save the current cursor position
				$x = $this -> GetX();
				$y = $this -> GetY();
				$w = $cell -> width;
				
				$this -> set_cell_format($cell);
				
				$this -> draw_cell_background($cell, $x, $y, $w, $h);
					
				// draw cell borders
				if (substr_count($cell -> border,'T')>0)
				{ $this -> Line($x, $y, $x+$w, $y); }

				if (substr_count($cell -> border,'B')>0)
				{ $this -> Line($x, $y+$h, $x+$w, $y+$h); }
				
				if (substr_count($cell -> border,'L')>0)
				{ $this -> Line($x, $y, $x, $y+$h); }
				
				if (substr_count($cell -> border,'R')>0)
				{ $this -> Line($x+$w, $y, $x+$w, $y+$h); }

				// print the text
				$this -> MultiCell($w, $cell -> height,
					$cell -> text, 0, $cell -> align, 0);
				
				// put the position to the right of the cell
				$this -> SetXY($x+$w, $y);
			}
			
			// go to the next line
			$this -> Ln($h);
		}	
	}
	
	// calculates the height of a row
	protected function row_height ($row)
	{
		$max_lines = 0; // maximum number of lines in the row
		$cell_height = 0; // height of the cells in the row
		
		foreach ($row as $cell)
		{
			// set cell format
			$this -> set_cell_format($cell);
			
			$max_lines = max (
					$max_lines,
					$this -> NbLines($cell -> width, $cell -> text)
			);
			$cell_height = $cell -> height;
		}
		return $cell_height * $max_lines;
	}
	
	// set format for a multi cell
	protected function set_cell_format($cell)
	{
		$this -> SetFont(
			$cell -> font_name,
			$cell -> font_style,
			$cell -> font_size
		);


		$color = explode(',',$cell -> fill_color);
		$this -> SetFillColor($color[0],$color[1],$color[2]);
		
		$color = explode(',',$cell -> text_color);
		$this -> SetTextColor($color[0],$color[1],$color[2]);
		
		$color = explode(',',$cell -> draw_color);
		$this -> SetDrawColor($color[0],$color[1],$color[2]);
		
		$this -> SetLineWidth($cell -> line_width);
	}
	
	protected function draw_cell_background($cell, $x, $y, $w, $h)
	{
		$color = explode(',',$cell -> fill_color);
		$this -> SetDrawColor($color[0],$color[1],$color[2]);
		
		$this -> Rect($x, $y, $w, $h, 'FD');

		$color = explode(',',$cell -> draw_color);
		$this -> SetDrawColor($color[0],$color[1],$color[2]);
		
	}

	
   // Computes the number of lines a MultiCell of width w will take
   protected function NbLines($w, $txt)
   {
      $cw=&$this->CurrentFont['cw'];
      if($w==0)
         $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r", '', $txt);
      $nb=strlen($s);
      if($nb>0 and $s[$nb-1]=="\n")
         $nb--;
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $nl=1;
      while($i<$nb)
      {
         $c=$s[$i];
         if($c=="\n")
         {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
         }
         if($c==' ')
            $sep=$i;
         $l+=$cw[$c];
         if($l>$wmax)
         {
            if($sep==-1)
            {
               if($i==$j)
                  $i++;
            }
            else
               $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
         }
         else
            $i++;
      }
      return $nl;
   }
		

	
} // Class csgPDFForm

?>