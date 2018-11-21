#include "mbed.h"
#include <string.h>
//#include "GSM.h"

Serial gsm(D8, D2);
Serial pc(USBTX, USBRX);
DigitalOut led(LED1);

DigitalIn button(USER_BUTTON);

Timer ajastin;

//Alku käskyt

char *alku[][2][200] = {
	{{"AT"},			{"OK"}},		//Varmistetaan että modeemi on päällä
	{{"AT+CMEE=2"},		{"OK"}},		//Virhee kirjallisiksi
	{{"AT+CPIN=0000"},	{"Call Ready"}},//Annetaan PIN koodi
	{{"AT+CGREG?"},		{"+CGREG: 0,1"}},//Varmistetaan että on rekisteröitynyt koti verkkoon.
	//{{"AT+CGATT=1"},	{"OK"}},		//Kiinnittää GPRS:n
	//{{"AT+QIMODE=1"},	{"OK"}},		//Aktivoidaan PDP konteksti
	{{"AT+CGACT=1,1"},	{"OK"}},		//Aktivoidaan PDP konteksti
	{{"AT+QIREGAPP"},	{"OK"}},		//Käynnistetään TCP/IP protocolla
	{{"AT+QIACT"},		{"OK"}},		//Aktivoidaan TCP/IP
	{{"AT+QILOCIP"},	{"OK"}}			//Luetaan oma IP
};

char *lahetys[][2][200] = {
	{{"AT+QIOPEN=\"TCP\",\"193.167.100.74\",80"}, {"CONNECT OK"}}, //Koulun IP
	{{"AT+QISEND"}, {">"}},
	//{{"GET /~t5pyan00/?viesti=GSM HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032"}, {"SEND OK"}}
	{{"GET /~tkorpela/jkaskitesti/vastaanotto.php?testi=SeToimii2 HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032"}, {"SEND OK"}}
};

void ledi();
void lahetaLueLoop(bool alku);
void lue(int aika);
void laheta(char *kasky);
void lahetaJaLue(char *kasky, int aika);
int lahetaJaOdota(char *kasky, char *vastaus, int aika);

float Lat = 65.00033, Lon = 25.50952, HDOP = 2, Nopeus = 12;
int Aika = 121412, ID = 3;
char viesti[1000];// = {"GET /~t6heja02/vastaanotto.php?testi=%d HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032"};

int main(){
	pc.printf("Aloitus. GSM serial pass.\n");
	
	//Ei mennä eteenpäin ennen napin painamista.
	lahetaLueLoop(true);
	
	int l = sizeof(alku)/sizeof(alku[0]);
	
	//Käydään läpi alku komentosarja
	for(int i = 0; i < l - 1; i++){
		ledi();
		int j = lahetaJaOdota(alku[i][0][0], alku[i][1][0], 5);
		lue(1);
		if(j != 0 ){
			pc.printf("Väärä vastaus.");
			i--;
		}
	}
	
	//Printtaa IP osoitteen.
	laheta(alku[l - 1][0][0]);
	
	lue(5);
	
	while(true){
		
		lahetaLueLoop(true);
		lue(1);
		
		//Luo IP yhteyden ja lähettää dataa sen läpi.
		
		l = sizeof(lahetys)/sizeof(lahetys[0]);
		for(int i = 0; i < l - 1; i++){
			int j = lahetaJaOdota(lahetys[i][0][0], lahetys[i][1][0], 10);
			lue(1);
			if(j != 0 ){
				pc.printf("Väärä vastaus.");
				i--;
			}
		}
		
		//Tapa millä saadaan yhdistettyä muuttujia char taulukkoon
		sprintf(viesti, "GET /~t6heja02/lisaa.php?ID=%d&Aika=%d&Lat=%f&Lon=%f&HDOP=%f&Nopeus=%f HTTP/1.1\r\nHost: www.students.oamk.fi\r\nConnection: close\r\n\r\n\032", ID, Aika, Lat, Lon, HDOP, Nopeus);
		
		lahetaJaOdota(viesti, "SEND OK", 10);
		
		
	
		
	}
}

void ledi(){
	led = !led;
}

//Kaikki mitä pc:lle kirjoitetaan lähetetään GSM:lle ja päinvastoin.
void lahetaLueLoop(bool alku){
	while(true){
		if(pc.readable()){
			ledi();
			while(pc.readable()){
				gsm.putc(pc.getc());
			}
		}
		if(gsm.readable()){
			ledi();
			while(gsm.readable()){
				pc.putc(gsm.getc());
			}
		}
		if(alku && !button){
			ledi();
			wait(0.5);
			if(button)break;
		}
	}
}

void lue(int aika){
	ajastin.start();
	while(true){
		if(gsm.readable()){
			char c = gsm.getc();
			pc.putc(c);
		}
		if(aika <= ajastin.read()){
			//pc.printf("----------AIKA:%f----------", ajastin.read());
			ajastin.stop();
			ajastin.reset();
			break;
		}
	}
}

void laheta(char *kasky){
	gsm.printf(kasky);
	gsm.puts("\r");
	//pc.puts(kasky);
	pc.puts("\n");
}

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
			ajastin.stop();
			ajastin.reset();
			break;
		}
	}
}

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
				return 0;
			}
			
		}
		if(aika < ajastin.read()){
			ajastin.stop();
			ajastin.reset();
			return -1;
		}
	}
}