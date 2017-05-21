<?
class SymconYahooWeather extends IPSModule
{
	
	public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		$this->CreateVarProfileYWHTemp();
        
		$this->RegisterPropertyString("YWHTown", "Konstanz");
		$this->RegisterPropertyInteger("YWHDays", 2);
        $this->RegisterPropertyInteger("YWHIntervall", 14400);
		$this->RegisterPropertyString("YWHTemperature","c");
		
		$this->RegisterVariableString("Wetter", "Wetter","~HTMLBox",1);
		
		// Vorhersage für heute als Variablen
		$this->RegisterVariableString("YWH_Wetter_heute", "Wettervorhersage (heute)");
		$this->RegisterVariableFloat("YWH_Heute_temp_min", "Temp (min)","YHW.Temp");
		$this->RegisterVariableFloat("YWH_Heute_temp_max", "Temp (max)","YHW.Temp");
		
        $this->RegisterTimer("UpdateSymconYahooWeather", 14400, 'YWH_Update($_IPS[\'TARGET\']);');
		
		// Inspired by module SymconTest/HookServe
		$this->RegisterHook("/hook/SymconYahooWeather");
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
	
	private function CreateVarProfileYWHTemp() {
		if (!IPS_VariableProfileExists("YHW.Temp")) {
			IPS_CreateVariableProfile("YHW.Temp", 1);
			IPS_SetVariableProfileValues("YHW.Temp", -100, 100, 1);
			IPS_SetVariableProfileDigits("YHW.Temp", 1);
			IPS_SetVariableProfileText("YHW.Temp", "", " °");
			IPS_SetVariableProfileAssociation("YHW.Temp", -100, "%1f", "", -1);

		 }
	}
	
	private function GenerateWeatherTable($Value){
    	$weekdays = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"); 
		$forecast = $Value->{'query'}->{'results'}->{'channel'}->{'item'}->{'forecast'};
		$temperature = strtoupper($this->ReadPropertyString("YWHTemperature"));
		
		
    	if( $Value->query->count > 0 ){
			$date=new DateTime('now'); 
			
			$vorhersage_heute = "";
			
			if ($forecast[0]->code == '0') $vorhersage_heute .= 'Tornado'; 
				if ($forecast[0]->code == '1') $vorhersage_heute .= 'Tropischer Sturm'; 
				if ($forecast[0]->code == '2') $vorhersage_heute .= 'Orkan'; 
				if ($forecast[0]->code == '3') $vorhersage_heute .= 'Heftiges Gewitter'; 
				if ($forecast[0]->code == '4') $vorhersage_heute .= 'Gewitter'; 
				if ($forecast[0]->code == '5') $vorhersage_heute .= 'Regen und Schnee'; 
				if ($forecast[0]->code == '6') $vorhersage_heute .= 'Regen und Eisregen'; 
				if ($forecast[0]->code == '7') $vorhersage_heute .= 'Schnee und Eisregen'; 
				if ($forecast[0]->code == '8') $vorhersage_heute .= 'Gefrierender Nieselregen'; 
				if ($forecast[0]->code == '9') $vorhersage_heute .= 'Nieselregen'; 
				if ($forecast[0]->code == '10') $vorhersage_heute .= 'Gefrierender Regen'; 
				if ($forecast[0]->code == '11') $vorhersage_heute .= 'Schauer'; 
				if ($forecast[0]->code == '12') $vorhersage_heute .= 'Schauer'; 
				if ($forecast[0]->code == '13') $vorhersage_heute .= 'Schneeflocken'; 
				if ($forecast[0]->code == '14') $vorhersage_heute .= 'Leichte Schneeschauer'; 
				if ($forecast[0]->code == '15') $vorhersage_heute .= 'St&uuml;rmiger Schneefall'; 
				if ($forecast[0]->code == '16') $vorhersage_heute .= 'Schnee'; 
				if ($forecast[0]->code == '17') $vorhersage_heute .= 'Hagel'; 
				if ($forecast[0]->code == '18') $vorhersage_heute .= 'Eisregen'; 
				if ($forecast[0]->code == '19') $vorhersage_heute .= 'Staub'; 
				if ($forecast[0]->code == '20') $vorhersage_heute .= 'Neblig'; 
				if ($forecast[0]->code == '21') $vorhersage_heute .= 'Dunst'; 
				if ($forecast[0]->code == '22') $vorhersage_heute .= 'Staubig'; 
				if ($forecast[0]->code == '23') $vorhersage_heute .= 'St&uuml;rmisch'; 
				if ($forecast[0]->code == '24') $vorhersage_heute .= 'Windig'; 
				if ($forecast[0]->code == '25') $vorhersage_heute .= 'Kalt'; 
				if ($forecast[0]->code == '26') $vorhersage_heute .= 'Bew&ouml;lkt'; 
				if ($forecast[0]->code == '27') $vorhersage_heute .= 'Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(nachts)'; 
				if ($forecast[0]->code == '28') $vorhersage_heute .= 'Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(tags&uuml;ber)'; 
				if ($forecast[0]->code == '29') $vorhersage_heute .= 'Teilweise bew&ouml;lkt (nachts)'; 
				if ($forecast[0]->code == '30') $vorhersage_heute .= 'Teilweise bew&ouml;lkt (tags&uuml;ber)'; 
				if ($forecast[0]->code == '31') $vorhersage_heute .= 'Klar (nachts)'; 
				if ($forecast[0]->code == '32') $vorhersage_heute .= 'Sonnig'; 
				if ($forecast[0]->code == '33') $vorhersage_heute .= 'Sch&ouml;n (nachts)'; 
				if ($forecast[0]->code == '34') $vorhersage_heute .= 'Sch&ouml;n (tags&uuml;ber)'; 
				if ($forecast[0]->code == '35') $vorhersage_heute .= 'Regen und Hagel'; 
				if ($forecast[0]->code == '36') $vorhersage_heute .= 'Hei&szlig;'; 
				if ($forecast[0]->code == '37') $vorhersage_heute .= 'Einzelne Gewitter'; 
				if ($forecast[0]->code == '38') $vorhersage_heute .= 'Vereinzelte Gewitter'; 
				if ($forecast[0]->code == '39') $vorhersage_heute .= 'Vereinzelte Gewitter'; 
				if ($forecast[0]->code == '40') $vorhersage_heute .= 'Vereinzelte Schauer'; 
				if ($forecast[0]->code == '41') $vorhersage_heute .= 'Starker Schneefall'; 
				if ($forecast[0]->code == '42') $vorhersage_heute .= 'Vereinzelte Schneeschauer'; 
				if ($forecast[0]->code == '43') $vorhersage_heute .= 'Starker Schneefall'; 
				if ($forecast[0]->code == '44') $vorhersage_heute .= 'Teilweise bew&ouml;lkt'; 
				if ($forecast[0]->code == '45') $vorhersage_heute .= 'Donnerregen'; 
				if ($forecast[0]->code == '46') $vorhersage_heute .= 'Schneeschauer'; 
				if ($forecast[0]->code == '47') $vorhersage_heute .= 'Einzelne Gewitterschauer';
				
			$this->SetValueString("YWH_Wetter_heute", $vorhersage_heute );
			$this->SetValueFloat("YWH_Heute_temp_min", $forecast[0]->low );
			$this->SetValueFloat("YWH_Heute_temp_max", $forecast[0]->high );
			
			// build table
			$weatherstring = '<table width="100%">';
			// build header
			$weatherstring .= '<tr>';
			
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">'; 
				$day = date("w")+$i;
				$weatherstring .= $weekdays[$day];
				$weatherstring .= '</td>';
			}
			$weatherstring .= '</tr>';
			
			// row with weather infos	

			$weatherstring .= '<tr>';
			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				$weatherstring .= '<img src="/hook/SymconYahooWeather/' .$forecast[$i]->code .'.png">';
				//@end todo: image with weather code
				$weatherstring .= '<br>';
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
				$weatherstring .= 'min ' .$forecast[$i]->low .' &deg;' .$temperature;
				$weatherstring .= '<br>';
				$weatherstring .= 'max ' .$forecast[$i]->high .' &deg;' .$temperature;
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
	
	private function RegisterHook($WebHook) {
		// Inspired from module SymconTest/HookServe
		$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
		if(sizeof($ids) > 0) {
			$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
			$found = false;
			foreach($hooks as $index => $hook) {
				if($hook['Hook'] == $WebHook) {
					if($hook['TargetID'] == $this->InstanceID)
						return;
					$hooks[$index]['TargetID'] = $this->InstanceID;
					$found = true;
				}
			}
			if(!$found) {
				$hooks[] = Array("Hook" => $WebHook, "TargetID" => $this->InstanceID);
			}
			IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
			IPS_ApplyChanges($ids[0]);
		}
	}
	
	protected function ProcessHookData() {
			// Inspired from module SymconTest/HookServe
			
			$root = realpath(__DIR__ . "/Images");
			//append index.html
			if(substr($_SERVER['REQUEST_URI'], -1) == "/") {
				$_SERVER['REQUEST_URI'] .= "index.html";
			}
						
			//reduce any relative paths. this also checks for file existance
			$path = realpath($root . "/" . substr($_SERVER['REQUEST_URI'], strlen("/hook/SymconYahooWeather/")));
			IPS_LogMessage("WebHook path: ", $path);
			if($path === false) {
				http_response_code(404);
				die("File not found!");
			}

			
			if(substr($path, 0, strlen($root)) != $root) {
				http_response_code(403);
				die("Security issue. Cannot leave root folder!");
			}
			header("Content-Type: ".$this->GetMimeType(pathinfo($path, PATHINFO_EXTENSION)));
			readfile($path);
		}
		
		private function GetMimeType($extension) {
			// Inspired from module SymconTest/HookServe
			$lines = file(IPS_GetKernelDirEx()."mime.types");
			foreach($lines as $line) {
				$type = explode("\t", $line, 2);
				if(sizeof($type) == 2) {
					$types = explode(" ", trim($type[1]));
					foreach($types as $ext) {
						if($ext == $extension) {
							return $type[0];
						}
					}
				}
			}
			return "text/plain";
		}
}
?>