<?
class SymconYahooWeather extends IPSModule
{
	
	public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        
		$this->RegisterPropertyString("YWHTown", "Konstanz");
		$this->RegisterPropertyInteger("YWHDays", 2);
        $this->RegisterPropertyInteger("YWHIntervall", 14400);
		$this->RegisterPropertyString("YWHTemperature","c");
		$this->RegisterPropertyInteger("YWHImageZoom", 100);
		$this->RegisterPropertyInteger("YWHDisplay", 1);
		
		
		$this->RegisterVariableString("Wetter", "Wetter","~HTMLBox",1);
		
		// Vorhersage für heute als Variablen
		$this->RegisterVariableString("YWH_Wetter_heute", "Wettervorhersage (heute)");
		$this->RegisterVariableFloat("YWH_Heute_temp_min", "Temp (min)","~Temperature");
		$this->RegisterVariableFloat("YWH_Heute_temp_max", "Temp (max)","~Temperature");
		
		$this->RegisterVariableString("YWH_Sonnenaufgang", "Sonnenaufgang (heute)");
		$this->RegisterVariableString("YWH_Sonnenuntergang", "Sonnenuntergang (heute)");
		
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
			IPS_SetVariableProfileValues("YHW.Temp", "-100,0", "100,0", "1,0");
			IPS_SetVariableProfileText("YHW.Temp", "", " °");
			IPS_SetVariableProfileAssociation("YHW.Temp", "-100,0", "%1f", "", -1);

		 }
	}
	
	private function GenerateWeatherTable($Value){
    	$weekdays = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"); 
		$forecast = $Value->{'query'}->{'results'}->{'channel'}->{'item'}->{'forecast'};
		
		$sonnenAufgang = $Value->{'query'}->{'results'}->{'channel'}->{'astronomy'}->{'sunrise'};
		$sonnenUntergang = $Value->{'query'}->{'results'}->{'channel'}->{'astronomy'}->{'sunset'};
		
		IPS_LogMessage("SymconYahooWeather", "strtotime-Sonnenaufgang: ". date("H:i:s",strtotime($sonnenAufgang)));
		IPS_LogMessage("SymconYahooWeather", "strtotime-Sonnenaufgang: ". date("H:i:s",strtotime($sonnenUntergang)));
		
		$sonnenAufgang = str_replace('am', 'Uhr', $sonnenAufgang);
		$sonnenUntergang = str_replace('pm', 'Uhr', $sonnenUntergang);
		
		
		IPS_LogMessage("SymconYahooWeather", "Sonnenaufgang: ". $sonnenAufgang);
		IPS_LogMessage("SymconYahooWeather", "Sonnenuntergang: ". $sonnenUntergang);
		
		$this->setValueString("YWH_Sonnenaufgang", $sonnenAufgang);
		$this->setValueString("YWH_Sonnenuntergang", $sonnenUntergang);
		
		$temperature = strtoupper($this->ReadPropertyString("YWHTemperature"));
		
		
    	if( $Value->query->count > 0 ){
			$date=new DateTime('now'); 
			
			$vorhersage_heute = "";
			
			IPS_LogMessage("SymconYahooWeather", "WeatherCondition: ". $this->getWeatherCondition($forecast[0]->code));
			
			$vorhersage_heute = $this->getWeatherCondition($forecast[0]->code);
				
			$this->SetValueString("YWH_Wetter_heute", $vorhersage_heute );
			$this->SetValueFloat("YWH_Heute_temp_min", $forecast[0]->low );
			$this->SetValueFloat("YWH_Heute_temp_max", $forecast[0]->high );
			
			$HTMLBoxType = $this->ReadPropertyInteger("YWHDisplay");
			
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
				
				if( $HTMLBoxType == 1 OR $HTMLBoxType == 2 ){
				
					$weatherstring .= '<td align="center">';
					$weatherstring .= '<img src="/hook/SymconYahooWeather/' .$forecast[$i]->code .'.png" style="height:' .$this->ReadPropertyInteger("YWHImageZoom") .'%;width:auto;">';
					$weatherstring .= '<br>';
				}
				if( $HTMLBoxType == 2 ){
					$weatherstring = $this->getWeatherCondition($forecast[$i]->code);
				}
				
				
			}
			$weatherstring .= '</td>';
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
		
		private function getWeatherCondition( $condition ){
			
			$weathercondition = array (
				"0" => "Tornado",
				"1" => "Tropischer Sturm", 
				"2" => "Orkan", 
				"3" => "Heftiges Gewitter", 
				"4" => "Gewitter", 
				"5" => "Regen und Schnee", 
				"6" => "Regen und Eisregen", 
				"7" => "Schnee und Eisregen", 
				"8" => "Gefrierender Nieselregen", 
				"9" => "Nieselregen", 
				"10" => "Gefrierender Regen", 
				"11" => "Schauer", 
				"12" => "Schauer", 
				"13" => "Schneeflocken", 
				"14" => "Leichte Schneeschauer", 
				"15" => "St&uuml;rmiger Schneefall", 
				"16" => "Schnee", 
				"17" => "Hagel", 
				"18" => "Eisregen", 
				"19" => "Staub", 
				"20" => "Neblig", 
				"21" => "Dunst", 
				"22" => "Staubig", 
				"23" => "St&uuml,rmisch", 
				"24" => "Windig", 
				"25" => "Kalt", 
				"26" => "Bew&ouml;lkt", 
				"27" => "Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(nachts)", 
				"28" => "Gr&ouml&szlig;tenteils bew&ouml;lkt<br>(tags&uuml;ber)", 
				"29" => "Teilweise bew&ouml;lkt (nachts)", 
				"30" => "Teilweise bew&ouml;lkt (tags&uuml;ber)", 
				"31" => "Klar (nachts)", 
				"32" => "Sonnig", 
				"33" => "Sch&ouml;n (nachts)", 
				"34" => "Sch&ouml;n (tags&uuml;ber)", 
				"35" => "Regen und Hagel", 
				"36" => "Hei&szlig;", 
				"37" => "Einzelne Gewitter", 
				"38" => "Vereinzelte Gewitter", 
				"39" => "Vereinzelte Gewitter", 
				"40" => "Vereinzelte Schauer", 
				"41" => "Starker Schneefall", 
				"42" => "Vereinzelte Schneeschauer", 
				"43" => "Starker Schneefall", 
				"44" => "Teilweise bew&ouml;lkt", 
				"45" => "Donnerregen", 
				"46" => "Schneeschauer", 
				"47" => "Einzelne Gewitterschauer",
				);
			return $weathercondition[$condition];
		}
}
?>