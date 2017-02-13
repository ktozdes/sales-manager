<?php
class PrintView{
	private $fileName;
	private $viewMode;
	private $sectionNames;
	
	function __construct($options,$sectionNames=null)
	{
		if (isset($options['file'])){
			$this->fileName = $options['file'];
			$this->viewMode = ($options['viewmode']=='print')?'print':'view';

			$this->sectionNames = $sectionNames;
			
		}
		else 
			$this->halt('output file is not not provided found');
	}
	
	function __destruct(){
		//this part was commented about, because of json
		//echo '</table></div></body></html>';
	}
	
	public function printHtml($options,$result=null,$customResult=null,$customLineNum=1)
	{
        global $siteData;
		foreach($options as $section){
			require $this->fileName;
		}
	}
	
	public function helloworld()
	{
		echo 'hello world from print view class';
	}
    private function halt($message)
    {
        echo '<br/>Print View error<br/>';
        echo $message;
        exit();
    }
}
?>