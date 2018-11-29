#include "mbed.h"
#include "rtos.h"
#include <string.h>

Serial gsm(D8, D2);
Serial gps(D14, D15);
Serial pc(USBTX, USBRX);
DigitalOut led(LED2);
//Thread gpsthread;
//Thread gsmthread;
DigitalIn button(USER_BUTTON);

Timer ajastin;

//Alku käskyt

char *alku[][2][50] = {
	{{"AT"},			{"OK"}},		//Varmistetaan että modeemi on päällä
	{{"AT+CMEE=2"},		{"OK"}},		//Virhee kirjallisiksi
	{{"AT+CPIN=0000"},	{"Call Ready"}},//Annetaan PIN koodi
	{{"AT+CGREG?"},		{"+CGREG: 0,1"}},//Varmistetaan että on rekisteröitynyt koti verkkoon.
	{{"AT+CGACT=1,1"},	{"OK"}},		//Aktivoidaan PDP konteksti
	{{"AT+QIREGAPP"},	{"OK"}},		//Käynnistetään TCP/IP protocolla
	{{"AT+QIACT"},		{"OK"}},		//Aktivoidaan TCP/IP
	{{"AT+QILOCIP"},	{"."}}			//Luetaan oma IP
};

char *lahetys[][2][50] = {
	{{"AT+QIOPEN=\"TCP\",\"193.167.100.74\",80"}, {"CONNECT OK"}}, //Koulun IP
	{{"AT+QISEND"}, {">"}}
};

//Globaaleja muuttujia tiedon siirtämiseksi GPS:ltä GSM:lle.
double Lat = 0.0, Lon = 0.0, oikeaHDOP = 0.0;
int Aika = 0, ID = 0;
char viesti[1000];
char gsmBuffer[300];

void GSM_Thread();
void GPS_func();
void ledi();
void lahetaLueLoop(bool alku);
void lue(int aika);
void laheta(char *kasky);
void lahetaJaLue(char *kasky, int aika);
int lahetaJaOdota(char *kasky, char *vastaus, int aika);
int lahetaJaOdota(char *kasky, char *vastaus, char *toinen, int aika);
int lueBufferiin(char *buffer, char *vastaus, int aika);
void yhdistaTCP(int miinus);


int main(){
	pc.baud(115200);
	gsm.baud(115200);
	pc.printf("Aloitus. GSM GPS yhdistelmä.\n");
	lahetaLueLoop(true);
	
	int kumpi = lahetaJaOdota("AT+CPIN?", "READY", "SIM PIN", 3);
	pc.printf("Kumpi:%d", kumpi);
	if(kumpi == 1){
		int l = sizeof(alku)/sizeof(alku[0]);
		//Käydään läpi alku komentosarja
		for(int i = 0; i < l; i++){
			int j = lahetaJaOdota(alku[i][0][0], alku[i][1][0], 10 );
			wait(0.5);
			if(j != 0 ){
				//pc.printf("Väärä vastaus.Alku");
				i--;
				wait(1);
			}
		}
	}
	wait(1);
	//Kysytään uusi ID.
	yhdistaTCP(0);
	
	lahetaJaOdota("GET /~t6heja02/lisaa.php HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032", "SEND OK", 10);
	
	lueBufferiin(&gsmBuffer[0], "CLOSED", 3);
	
	//pc.printf("---------------------Luettu.");
	//pc.printf(gsmBuffer);
	char *gsmToken;
	strtok(gsmBuffer, "$");
	gsmToken = strtok(NULL, "\n");
	ID = atoi(gsmToken) + 1;
	pc.printf("\nUusi ID:%d\n", ID);
	
	//Odotetaan GPS.
	while(true){
		GPS_func();
		if(Lat != 0 && Lon != 0){
			break;
		}
	}
	
	//Yhdistetään TCP/IP yhteys.
	
	while(true){
		pc.printf("\nAlku\n");
		ledi();
		GPS_func();
		if(floor(Lon) == 0.0 || floor(Lat) == 0.0){
			printf("Virheellistä dataa/Heikko signaali");
		}
		else{
			yhdistaTCP(0);//qiopen ja qisend
			sprintf(viesti, "GET /~t6heja02/lisaa.php?ID=%d&Aika=%d&Lat=%f&Lon=%f&HDOP=%f HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032", ID, Aika, Lat, Lon, oikeaHDOP);
			//lahetaJaOdota("AT+QISEND", ">", 5);
			lahetaJaOdota(viesti, "SEND OK", 5);
			lahetaJaOdota("", "CLOSED", 10);
		}
		ledi();
		pc.printf("\nOdotus\n");
		wait(1);
	}
	
}

void GSM_Thread(){	
	
}

void GPS_func(){
		char buffer[460];
		for(int i = 0; i< 450; i++)
		{
		
			char c = gps.getc();
			
			buffer[i] = c;
		
		
		}
		
		//pc.puts(buffer);		//tulostaa bufferin raakadataa
	
		//Haluttu input = "$GPGGA,xxxxx.xx,xxxx.xxxxxx,x,xxxxxx.xxxxx,x,x,xx,x.xx,xx.x,x,xx.x,x,,*xx";
		
		char *token;		
		token = strtok(buffer, "$");
		
		//pc.printf("TYHJÄ = %s\n", token);
			
		char* paikannusTieto = {"GPGGA"};
	
		while (true){
			
			bool loyty = false;
			
			token = strtok(NULL, "$");
			
			for(int i = 0; i < 5; i++)
			{
				if (paikannusTieto[i] != token[i])
				{
					//pc.printf("Väärä merkki!\n");
					loyty = true;
					break;
				}
			}
			if (!loyty){
				
			break;				//Takaisin loopin alkuun etsimään "GPGGA"
			}

		}
		while (gps.readable())
		{
			char c = gps.getc();
		}
		token = strtok(token, ",.");
		//pc.printf(" = %s\n", token);
			
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int fix = atoi(token);
		int UTC = fix + 20000;
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int nolla = atoi(token);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int kulma = (atoi(token));					//JAA 100
		double todellinenKulma = floor(kulma/100);	// 65 !!
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		double minuutit = atoi(token);
		double todellisetMinuutit = minuutit/100000;	//0.0232000
		
		todellisetMinuutit = (kulma - todellinenKulma * 100)+todellisetMinuutit;
		
		double latitude = todellinenKulma + (todellisetMinuutit / 60);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int Korkeus = atoi(token);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		double a6 = atoi(token);						//2530.000000				
		double LeveysKulma = a6/100;
		LeveysKulma = floor(LeveysKulma);			//Pyöristää kahden desimaalin kokonaisluvuksi (floor=alaspäin pyöristys)
		
		double valivaihe = a6 - (LeveysKulma * 100);	// = 30 MINUUTIT
		
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		double a7 = atoi(token);
		double leveydenAika= a7/100000;				// 0,55534
		
		double longitude = LeveysKulma + (valivaihe + leveydenAika) / 60;
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);			//EAST
	//	int leveysSuunta = atoi(token);
		
		token = strtok(NULL, ",.");
	//	pc.printf("token = %s\n", token);
	//	int a9 = atoi(token);						//fix quality
		
		token = strtok(NULL, ",.");
	//	pc.printf("token = %s\n", token);
	//	int a10 = atoi(token);						//number of satellites
		
		token = strtok(NULL, ",.");
	//	pc.printf("token = %s\n", token);
		double HDOP = atoi(token);					//HDOP
		
		token = strtok(NULL, ",.");
	//	pc.printf("token = %s\n", token);			//Altitude from sea level
	//	int a12 = atoi(token);
		
		//pc.printf("\naika = %d\n longitude = %f\n Latitude = %f HDOP= %f\n\n\n\n",UTC, longitude, latitude, HDOP);
		
		Lat = latitude;
		Aika = UTC;
		Lon = longitude;
		oikeaHDOP = HDOP;
}

void ledi(){
	led = !led;
}

//Kaikki mitä pc:lle kirjoitetaan lähetetään GSM:lle ja päinvastoin.
void lahetaLueLoop(bool alku){
	while(true){
		if(pc.readable()){
			while(pc.readable()){
				gsm.putc(pc.getc());
			}
		}
		if(gsm.readable()){
			while(gsm.readable()){
				pc.putc(gsm.getc());
			}
		}
		if(alku && !button){//Poistutaan loopista napin painalluksella jos alku == true.
			wait(0.5);
			if(button)break;
		}
	}
}

//Vaihto ehto wait fuktiolle mikä kuitenkin lukee tuloksia gsm:ltä.
void lue(int aika){
	pc.printf("------------------------------------------------------");
	ajastin.start();
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
		}
		if(aika <= ajastin.read()){
			ajastin.stop();
			ajastin.reset();
			break;
		}
	}
}

//Lähettää käskyn gsm:lle.
void laheta(char *kasky){
	gsm.printf(kasky);
	gsm.puts("\r");
	//pc.printf(kasky);
	pc.puts("\n");
}

//Lähettää käskyn gsm:lle ja lukee vastauksia tietyn aikaa.
void lahetaJaLue(char *kasky, int aika){
	laheta(kasky);
	ajastin.start();
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
			//if(c == "\r")break;
		}
		if(aika < ajastin.read()){
			wait(0.1);
			ajastin.stop();
			ajastin.reset();
			break;
		}
	}
}

//Lähettää käskyn gsm:lle ja odottaa tiettyä vastausta.
//Jos vastaus saadaan palauttaa 0, jos ei saada tietyssä ajassa palauttaa 1.
int lahetaJaOdota(char *kasky, char *vastaus, int aika){
	laheta(kasky);
	int indeksi = 0, pituus = strlen(vastaus);
	ajastin.start();
	
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
			if(c == vastaus[indeksi]){
				indeksi++;
			}
			else{
				indeksi = 0;
			}
			if(pituus == indeksi){
				ajastin.stop();
				ajastin.reset();
				return 0;
			}
			
		}
		if(aika < ajastin.read()){
			wait(0.1);
			ajastin.stop();
			ajastin.reset();
			return -1;
		}
	}
}

int lahetaJaOdota(char *kasky, char *vastaus, char *toinen, int aika){
	laheta(kasky);
	int indeksi = 0, pituus = strlen(vastaus);
	int indeksi2 = 0, pituus2 = strlen(toinen);
	ajastin.start();
	
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
			if(c == vastaus[indeksi]){
				indeksi++;
			}
			else{
				indeksi = 0;
			}
			
			if(c == toinen[indeksi2]){
				indeksi2++;
			}
			else{
				indeksi2 = 0;
			}
			
			if(pituus == indeksi){
				wait(0.1);
				ajastin.stop();
				ajastin.reset();
				return 0;
			}
			if(pituus2 == indeksi2){
				wait(0.1);
				ajastin.stop();
				ajastin.reset();
				return 1;
			}
			
		}
		if(aika < ajastin.read()){
			wait(0.1);
			ajastin.stop();
			ajastin.reset();
			return -1;
		}
	}
}

//Lähettää käskyn gsm:lle, odottaa tiettyä vastausta ja kirjoittaa kaiken lukemansa bufferiin.
int lueBufferiin(/*char *kasky, */char *buffer, char *vastaus, int aika){
	//laheta(kasky);
	int indeksi = 0, pituus = strlen(vastaus);
	ajastin.start();
	int j = 0;
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
			buffer[j] = c;
			j++;
			if(c == vastaus[indeksi]){
				indeksi++;
			}
			else{
				indeksi = 0;
			}
			if(pituus == indeksi){
				return 0;
			}
			
		}
		if(aika < ajastin.read()){
			wait(0.1);
			ajastin.stop();
			ajastin.reset();
			return -1;
		}
	}
}

void yhdistaTCP(int miinus){
	int l = sizeof(lahetys)/sizeof(lahetys[0]);
	for(int i = 0; i < l - miinus; i++){
		int j = lahetaJaOdota(lahetys[i][0][0], lahetys[i][1][0], "ALREADY CONNECT", 5);
		wait(0.1);
		if(j == -1){
			//pc.printf("Väärä vastaus.TCP");
			wait(1);
			i--;
		}
	}
}
