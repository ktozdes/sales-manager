<?php
/*excel writer*/
class ExcelController{
	private $phpExcel;
	private $fileName;
	private $row;
	private $column;
	private $minRow;
	private $minColumn;
	private $autoWidth;
	private $columnLength;
	
	
	function __construct($customParameters){
		if (isset($customParameters['file'])){
			$objReader = new PHPExcel_Reader_Excel5();
			$filname="Excel/".$customParameters['file'];
			$this->phpExcel = $objReader->load($filname);
		}
		else{
			$this->phpExcel = new PHPExcel();
		}
		if (isset($this->phpExcel) || $this->phpExcel!=null)
		{
			if (!isset($customParameters['file'])){
				$this->phpExcel->getActiveSheet()->setTitle("Sheet 1");
				$this->phpExcel->setActiveSheetIndex(0);
			}
			$this->fileName = (isset($customParameters['filename'])? $customParameters['filename'] : 'file' ).'.xls';
			$this->row = (isset($customParameters['row']))? $customParameters['row'] :1;
			$this->column = (isset($customParameters['column']))? $customParameters['column'] :0;
			$this->minRow = (isset($customParameters['minRow']))? $customParameters['minRow'] :1;
			$this->minColumn = (isset($customParameters['minColumn']))? $customParameters['minColumn'] :0;
			$this->autoWidth = (isset($customParameters['autoWidth']))? $customParameters['autoWidth'] : false;
			$this->columnLength = (isset($customParameters['columnLength']))? $customParameters['columnLength'] : 0;
		}
		else
			$this->halt('cannot connect to excel library.');
	}
	
	public function getCurrentColumn()
	{
		return $this->column;
	}
	public function getCurrentRow()
	{
		return $this->row;
	}
	public function insertImage($params)
	{
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Water_Level');
		$objDrawing->setDescription('Water_Level');
		$objDrawing->setPath($params['imageUrl']);
		if (isset($params['width']))
			$objDrawing->setHeight($params['width']);
		if (isset($params['height']))
			$objDrawing->setWidth($params['height']);
		$objDrawing->setCoordinates(((isset($params['coor'])?$params['coor']:'A1')));
		$objDrawing->setWorksheet($this->phpExcel->getActiveSheet());
	}
	public function addHeader($params)
	{
		if (isset($params[logo])){
			$objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($params[logo]);
			$objDrawing->setHeight(30);
			$this->phpExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_CENTER);
		}
		
		//$this->phpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L '. $params['left'] . '&R '.$params['right']);
		$this->phpExcel->getActiveSheet()->getHeaderFooter()->setEvenHeader('&L '. $params['left'] . '&R '.$params['right']);
	}

	public function addFooter($params)
	{
		$this->phpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L '.$params['left'] . '&R Page &P of &N');
		$this->phpExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter('&L '.$params['left'] . '&R '.$params['right'] . ' Page &P of &N');
	}
	
	public function writeLine($values,$formats=null,$coord=null){
		$this->formatController($formats);
		if ($coord!=null || $coord!=''){
			if (isset($coord['row']))
				$this->row = $coord['row'];
			if (isset($coord['column']))
				$this->column = $coord['column'];
		}
		
		switch (gettype($values)){
			case 'array':
				foreach($values as $key=>$value){
					$this->phpExcel->getActiveSheet()->setCellValueByColumnAndRow($this->column, $this->row,  $value);
					$this->column++;
				}
				break;
			default:
				//echo 'encoding is:'.mb_detect_encoding($values).$values.' '.$this->row.'-'.$this->column.' ';
				$this->phpExcel->getActiveSheet()->setCellValueByColumnAndRow($this->column, $this->row, $values);
				$this->column++;
				break;
		}
		$this->row++;
		$this->column=$this->minColumn;
	}
	
	
	
	function write($values,$formats=null,$coord = null){
		$this->formatController($formats);
		if ($coord!=null || $coord!=''){
			if (isset($coord['row']))
				$this->row = $coord['row'];
			if (isset($coord['column']))
				$this->column = $coord['column'];
		}
		
		switch (gettype($values)){
			case 'array':
				for ($i = 0; $i < count($values);$i++){
					$this->phpExcel->getActiveSheet()->setCellValueByColumnAndRow($this->column, $this->row, iconv( 'ISO-8859-9', 'UTF-8', $values[$i]));
					$this->column++;
				}
				break;
			default:
				//echo 'encoding is:'.mb_detect_encoding($values).$values.' '.$this->row.'-'.$this->column.' ';
				$this->phpExcel->getActiveSheet()->setCellValueByColumnAndRow($this->column, $this->row, iconv( 'ISO-8859-9', 'UTF-8',$values));
				$this->column++;
				break;
		}
	}
	
	public function finish(){
		if ($this->autoWidth)
			$this->setAutoWidth();
		$this->setFooter();
	}
	
	public function helloworld()
	{
		echo 'hello world from excel controller';
	}
	
	private function mergeCells()
	{
		
	}
	//accept array in following type: array(array('style'=>'font'))
	private function formatController($formats)
	{
        if ($formats==null)
            return false;
		foreach ($formats as $key=>$format){
			if ($key=='borderStyle'){
				$styleArray = array(
					'borders' => array(
						"outline" => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => $format[color]),
						),
					),
				);
			}
			if ($key=='fillStyle'){
				$styleArray = array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb'=>$format[color]),
					),
				);
			}
			if ($key=='fontStyle'){
				$styleArray = array(
					'font' => array(
						'bold' 		=> (isset($format['bold'])?true:false),
						'italic'    => (isset($format['italic'])?true:null),
						'name'      =>(isset($format['font'])?$format['font']:null),
						'size' 		=>(isset($format['size'])?$format['size']:null),
						'underline' => (isset($format['underline'])?PHPExcel_Style_Font::UNDERLINE_DOUBLE:""),
						'color'     => array(
							'rgb' 		=> (isset($format['color'])?$format['color']:"000000")
						)
					),
				);
			}
			$startCell = PHPExcel_Cell::stringFromColumnIndex(0) . $this->row;
			$endCell =   PHPExcel_Cell::stringFromColumnIndex($this->columnLength-1) . $this->row;
			$this->phpExcel->getActiveSheet()->getStyle("$startCell:$endCell")->applyFromArray($styleArray);
		}
	}
	private function formatCell($format){
		//echo (' formating cell ');
		for ($i=1;$i<count($format);$i++){
			if ($format['style'] == 'borders')
			{
				$index = $format[$i];
				$styleArray = array(
					'borders' => array(
						"$index" => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000'),
						),
					),
				);
			}
			$currentCell = PHPExcel_Cell::stringFromColumnIndex($this->column) . $this->row;
			//echo $currentCell;
			$this->phpExcel->getActiveSheet()->getStyle("$currentCell")->applyFromArray($styleArray);
		}
	}
	private function formatRow($format)
	{
		//echo (' formating row ');
		if ($format['style'] == 'borders')
		{
			$styleArray = array(
				'borders' => array(
					"outline" => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000'),
					),
				),
			);
		}
		elseif ($format['style'] == 'fill')
		{
			$styleArray = array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'C0C0C0'),
				),
			);
		}
		elseif ($format['style'] == 'font')
		{
			$styleArray = array(
				'font' => array(
					'bold' 		=> (isset($format['bold'])?true:false),
					'name'      =>(isset($format['font'])?$format['font']:null),
      				'size' 		=>(isset($format['size'])?$format['size']:null),
					'italic'    => (isset($format['italic'])?$format['italic']:null),
      				'underline' => (isset($format['underline'])?PHPExcel_Style_Font::UNDERLINE_DOUBLE:""),
      				'color'     => array(
      				'rgb' 		=> (isset($format['color'])?$format['color']:"000000")
      			)
			),
			);
			//print_r($styleArray);echo '<br/>';
		}
		$startCell = PHPExcel_Cell::stringFromColumnIndex(0) . $this->row;
		$endCell = PHPExcel_Cell::stringFromColumnIndex(0+$this->columnLength-1) . $this->row;
		//echo $this->columnLength." $startCell:$endCell";
		$this->phpExcel->getActiveSheet()->getStyle("$startCell:$endCell")->applyFromArray($styleArray);
	}
	
	private function setAutoWidth()
	{
		$highestColumm = $this->phpExcel->getActiveSheet()->getHighestColumn();
		for ($tempColumn = 'A'; $tempColumn != $highestColumm; $tempColumn++) {
			$this->phpExcel->getActiveSheet()->getColumnDimension("$tempColumn")->setAutoSize(true);
		}
		
	}
	
	private function setFooter(){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.$this->fileName.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, "Excel5");
		$objWriter->save("php://output");
		exit;
	}
	
	private function halt($message){
		echo 'Excel error occurred: '.$message;
		die( " Session halted." );
	}
}
?>