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
 	/*	
        $pegelUrl = "https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/" .$this->ReadPropertyString("PEGELSTATION") ."/W/currentmeasurement.json";
		
		$pegelDataJSON = @file_get_contents($pegelUrl);
		$pegelData = json_decode($pegelDataJSON);
		
		$pegelStandAktuell = $pegelData->value;
		$this->SetValueFloat("Pegelaktuell", $pegelStandAktuell);
		
		$pegelTendenzAktuell = $pegelData->trend;
		$this->SetValueInt("Tendenz", $pegelTendenzAktuell);
         * 
         */
		 
		$this->SetValueString("Wetter", "test" .time() );
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
		
	
}
?>