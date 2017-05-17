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
			$date=new DateTime('now'); 
			 
			// build table
			$weatherstring = '<table width="100%">';
			// build header
			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$date->modify('+' .$i .' day'); 
				$weatherstring .= '<td align="center">';
				$weatherstring .= $date->format('d.m.Y');
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			// row with weather infos	

			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				//@todo: image with weather code
				$weatherstring .= $forecast[$i]->code;
				$weatherstring .= '<br>';
				//@todo: replace weather code with beautiful text
				if ($forecast[$i]->code == '0') $weatherstring .= 'Tornado'; 
				if ($forecast[$i]->code == '1') $weatherstring .= 'Tropischer Sturm'; 
				if ($forecast[$i]->code == '2') $weatherstring .= 'Orkan'; 
				if ($forecast[$i]->code == '3') $weatherstring .= 'Heftiges Gewitter'; 
				if ($forecast[$i]->code == '4') $weatherstring .= 'Gewitter'; 
				if ($forecast[$i]->code == '5') $weatherstring .= 'Regen und Schnee'; 
				if ($forecast[$i]->code == '6') $weatherstring .= 'Regen und Eisregen'; 
				if ($forecast[$i]->code == '7') $weatherstring .= 'Schnee und Eisregen'; 
				if ($forecast[$i]->code == '8') $weatherstring .= 'Gefrierender Nieselregen'; 
				if ($forecast[$i]->code == '9') $weatherstring .= 'Nieselregen'; 
				if ($forecast[$i]->code == '10') $weatherstring .= 'Gefrierender Regen'; 
				if ($forecast[$i]->code == '11') $weatherstring .= 'Schauer'; 
				if ($forecast[$i]->code == '12') $weatherstring .= 'Schauer'; 
				if ($forecast[$i]->code == '13') $weatherstring .= 'Schneeflocken'; 
				if ($forecast[$i]->code == '14') $weatherstring .= 'Leichte Schneeschauer'; 
				if ($forecast[$i]->code == '15') $weatherstring .= 'St&uuml;rmiger Schneefall'; 
				if ($forecast[$i]->code == '16') $weatherstring .= 'Schnee'; 
				if ($forecast[$i]->code == '17') $weatherstring .= 'Hagel'; 
				if ($forecast[$i]->code == '18') $weatherstring .= 'Eisregen'; 
				if ($forecast[$i]->code == '19') $weatherstring .= 'Staub'; 
				if ($forecast[$i]->code == '20') $weatherstring .= 'Neblig'; 
				if ($forecast[$i]->code == '21') $weatherstring .= 'Dunst'; 
				if ($forecast[$i]->code == '22') $weatherstring .= 'Staubig'; 
				if ($forecast[$i]->code == '23') $weatherstring .= 'St&uuml;rmisch'; 
				if ($forecast[$i]->code == '24') $weatherstring .= 'Windig'; 
				if ($forecast[$i]->code == '25') $weatherstring .= 'Kalt'; 
				if ($forecast[$i]->code == '26') $weatherstring .= 'Bew&ouml;lkt'; 
				if ($forecast[$i]->code == '27') $weatherstring .= 'Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(nachts)'; 
				if ($forecast[$i]->code == '28') $weatherstring .= 'Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(tags&uuml;ber)'; 
				if ($forecast[$i]->code == '29') $weatherstring .= 'Teilweise bew&ouml;lkt (nachts)'; 
				if ($forecast[$i]->code == '30') $weatherstring .= 'Teilweise bew&ouml;lkt (tags&uuml;ber)'; 
				if ($forecast[$i]->code == '31') $weatherstring .= 'Klar (nachts)'; 
				if ($forecast[$i]->code == '32') $weatherstring .= 'Sonnig'; 
				if ($forecast[$i]->code == '33') $weatherstring .= 'Sch&ouml;n (nachts)'; 
				if ($forecast[$i]->code == '34') $weatherstring .= 'Sch&ouml;n (tags&uuml;ber)'; 
				if ($forecast[$i]->code == '35') $weatherstring .= 'Regen und Hagel'; 
				if ($forecast[$i]->code == '36') $weatherstring .= 'Hei&szlig;'; 
				if ($forecast[$i]->code == '37') $weatherstring .= 'Einzelne Gewitter'; 
				if ($forecast[$i]->code == '38') $weatherstring .= 'Vereinzelte Gewitter'; 
				if ($forecast[$i]->code == '39') $weatherstring .= 'Vereinzelte Gewitter'; 
				if ($forecast[$i]->code == '40') $weatherstring .= 'Vereinzelte Schauer'; 
				if ($forecast[$i]->code == '41') $weatherstring .= 'Starker Schneefall'; 
				if ($forecast[$i]->code == '42') $weatherstring .= 'Vereinzelte Schneeschauer'; 
				if ($forecast[$i]->code == '43') $weatherstring .= 'Starker Schneefall'; 
				if ($forecast[$i]->code == '44') $weatherstring .= 'Teilweise bew&ouml;lkt'; 
				if ($forecast[$i]->code == '45') $weatherstring .= 'Donnerregen'; 
				if ($forecast[$i]->code == '46') $weatherstring .= 'Schneeschauer'; 
				if ($forecast[$i]->code == '47') $weatherstring .= 'Einzelne Gewitterschauer';

				//$weatherstring .= $forecast[$i]->code;
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			// row with weather temperature			
			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				$weatherstring .= $forecast[$i]->low .' &deg;' .$temperature;
				$weatherstring .= '<br>';
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