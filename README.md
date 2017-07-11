### IP-Symcon Modul SymconYahooWeather

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Befehlsreferenz](#4-befehlsreferenz)
5. [Changelog](#5-changelog) 


## 1. Funktionsumfang

Kleines Modul, eine grafische Wetteranzeige von Yahoo Weather anzeigt.
Inspiriert durch: http://www.code-naschen.de/2011/11/wetter-api-fur-php-yahoo-weather.html 


## 2. Systemanforderungen
- IP-Symcon ab Version 4.2


## 3. Installation
Über die Kern-Instanz "Module Control" folgende URL hinzufügen:

`https://github.com/nik78476/SymconYahooWeather.git`

Die neue Instanz findet ihr dort, wo ihr sie angelegt habt.

Konfiguration:

Anzeige:

Das Modul erzeugt eine Variable mit Standardprofil ~HTML-Box, welche im Webfront
angezeigt werden kann. Die Anzeige der Bilder erfolgt über einen Webhook. Für die
HTMLBox kann eingestellt werden ob Bild oder Bild und Text angezeigt werden. 


Parameter | Beschreibung
------ | ---------------------------------
Name der Stadt | Stadt eingeben (Default: Konstanz)
Intervall | Angabe in Millisekunden (Default: 14400))
Temperaturanzeige | Auswahl Celsius oder Fahrenheit (Default: Celsius))
Anzeige Tage | Auswahl 1-5 Tage (Default: 2))
Anzeige HTMLBox | Auswahl Anzeige mit oder ohne Bild (Default: Bild und Text))
Bild-Zoom (%) | Zoom-Faktor für das Anzeigebild (Default: 100, Werte in %))

Das Modul legt folgende Variablen an:


Variable | Typ | Beschreibung
------ | ---------------------------------
Wetter | String | HTML Darstellung der Vorhersage gem. Einstellungen
Luftdruck (heute) | String | Anzeige Luftdruck in Millibar für den aktuellen Tag
Luftfeuchtigkeit (heute) | String | Anzeige Luftfeuchtigkeit in % für den aktuellen Tag
Sichtweite (heute) | String | Anzeige Sichtweite in km für den aktuellen Tag
Sonnenaufgang (heute) | String | Anzeige Uhrzeit Sonnenaufgang für den aktuellen Tag
Sonnenuntergang (heute) | String | Anzeige Uhrzeit Sonnenuntergang für den aktuellen Tag
Temp (max) | String | Anzeige Maximaltemperatur für den aktuellen Tag
Temp (min) | String | Anzeige Minimaltemperatur für den aktuellen Tag
Wettervorhersage (heute) | String | Wettervorhersage in Textform für den aktuellen Tag
Windgeschwindigkeit (heute) | String | Anzeige Windgeschwindigkeit in km/h für den aktuellen Tag



## 4. Befehlsreferenz

keine Befehle

## 5. Changelog

v1.0 first release

v1.1 update image zoom

v1.2 update Variables and Single-Image-Mode
