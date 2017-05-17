<?
class SymconYahooWeather extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
	
        
		$this->RegisterPropertyString("YWHTown", "London");
		$this->RegisterPropertyInteger("YWHDays", 2);
        $this->RegisterPropertyInteger("YWHIntervall", 14400);
		$this->RegisterPropertyString("YWHTemperature","c");
		
		$this->RegisterVariableString("Wetter", "Wetter","~HTMLBox",1);
		
        $this->RegisterTimer("UpdateSymconYahooWeather", 14400, 'YWH_Update($_IPS[\'TARGET\']);');
    }
    public function Destroy()
    {
        //Never delete this line!!
        parent::Destroy();
    }
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        
        $this->Update();
        $this->SetTimerInterval("UpdateSymconYahooWeather", $this->ReadPropertyInteger("YWHIntervall"));
    }
    public function Update()
    {
		// get Data from Yahoo Weather
		$weatherDataJSON = $this->QueryWeatherData();
		
		// build nice layout
		$this->SetValueString("Wetter", $this->GenerateWeatherTable($weatherDataJSON) );
		
    }

    private function SetValueInt($Ident, $Value){
    	$id = $this->GetIDforIdent($Ident);
    	SetValueInteger($id, $Value);
    	return true;	
  	}
	
	private function SetValueFloat($Ident, $Value)
	{
    	$id = $this->GetIDforIdent($Ident);
    	SetValueFloat($id, $Value);
    	return true;
  	}
   
    private function SetValueString($Ident, $Value)
    {
    	$id = $this->GetIDforIdent($Ident);
    	SetValueString($id, $Value);
    	return true;
  	}
	
	private function GenerateWeatherTable($Value){
    	$forecast = $Value->{'query'}->{'results'}->{'channel'}->{'item'}->{'forecast'};
		$temperature = strtoupper($this->ReadPropertyString("YWHTemperature"));
		
		
    	if( $Value->query->count > 0 ){
			// build table
			$weatherstring = '<table width="100%">';
			// build header
			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				$weatherstring .= $i;
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			// row with weather infos	
			$date=new DateTime('now'); 
			 
			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				
				$date->modify('+' .$i .' day'); 
			
				$weatherstring .= '<td align="center">';
				//@todo: image with weather code
				$weatherstring .= $date->format('d-m-Y');
				$weatherstring .= '<br>';
				//@todo: replace weather code with beautiful text
				$weatherstring .= $forecast[$i]->code;
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			// row with weather temperature			
			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				//@todo: image with weather code
				$weatherstring .= $forecast[$i]->low .' &deg;' .$temperature;
				$weatherstring .= '<br>';
				//@todo: replace weather code with beautiful text
				$weatherstring .= $forecast[$i]->high .' &deg;' .$temperature;
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			
			
			// finish table
			$weatherstring .= '</table>';
			return $weatherstring;
		} 
		else return "Weather is not available";
  	}
		
	private function QueryWeatherData(){
    	$BASE_URL = "http://query.yahooapis.com/v1/public/yql"; 
		$yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="' .$this->ReadPropertyString("YWHTown") .'") and u="' .$this->ReadPropertyString("YWHTemperature") .'"'; 
		$yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json"; 

		$jsonDataFromURL = @file_get_contents($yql_query_url);
		return json_decode($jsonDataFromURL);
  	}
}
?>