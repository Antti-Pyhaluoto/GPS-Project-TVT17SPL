#include "mbed.h"
//#include "GSM.h"

Serial gsm(D14, D15);
Serial pc(USBTX, USBRX);
DigitalOut led(LED1);

DigitalIn button(USER_BUTTON);

Timer ajastin;

//Alku käskyt

char *alku[][2][200] = {
	{{"AT"},			{"OK"}},		//Varmistetaan että modeemi on päällä
	{{"AT+CPIN=0000"},	{"Call Ready"}},//Annetaan PIN koodi
	{{"AT+CGREG?"},		{"+CGREG: 0,1"}},//Varmistetaan että on rekisteröitynyt koti verkkoon.
	//{{"AT+CGATT=1"},	{"OK"}},		//Kiinnittää GPRS:n
	//{{"AT+QIMODE=1"},	{"OK"}},		//Aktivoidaan PDP konteksti
	{{"AT+CGACT=1,1"},	{"OK"}},		//Aktivoidaan PDP konteksti
	{{"AT+QIREGAPP"},	{"OK"}},		//Käynnistetään TCP/IP protocolla
	{{"AT+QIACT"},		{"OK"}},		//Akticoidaan TCP/IP
	{{"AT+QILOCIP"},	{"OK"}}			//Luetaan oma IP
};

char *lahetys[][2][200] = {
	{{"AT+QIOPEN=\"TCP\",\"193.167.100.74\",80"}, {"CONNECT OK"}},
	{{"AT+QISEND"}, {">"}},
	{{"GET /testwifi/index.html HTTP/1.1\nHost: wifitest.adafruit.com\nConnection: close\032"}, {"SEND OK"}}
};

void lahetaLueLoop(bool alku);
void lue(int aika);
void laheta(char *kasky);
void lahetaJaLue(char *kasky, int aika);
int lahetaJaOdota(char *kasky, char *vastaus, int aika);

int main(){
	pc.printf("Aloitus. GSM serial pass.\n");
	//gsm.printf("AT\r");
	
	//Ei mennä eteenpäin ennen napin painamista.
	lahetaLueLoop(true);
	
	int l = sizeof(alku)/sizeof(alku[0]);
	
	//Käydään läpi alku komentosarja
	for(int i = 0; i < l - 1; i++){
		int j = lahetaJaOdota(alku[i][0][0], alku[i][1][0], 5);
		wait(0.5);
		if(j != 0 ){
			pc.printf("Väärä vastaus.");
			i--;
		}
	}
	
	//Printtaa IP osoitteen.
	laheta(alku[l - 1][0][0]);
	
	lue(5);
	while(true){
		//Luo IP yhteyden ja lähettää dataa sen läpi.
		l = sizeof(lahetys)/sizeof(lahetys[0]);
		for(int i = 0; i < l; i++){
			int j = lahetaJaOdota(lahetys[i][0][0], lahetys[i][1][0], 5);
			wait(0.5);
			if(j != 0 ){
				pc.printf("Väärä vastaus.");
				i--;
			}
		}
		
		//pc.printf("\nValmis\n");
	
		lahetaLueLoop(true);
	}
	/*while(true){
		if(pc.readable()){
			led = !led;
			while(pc.readable()){
				gsm.putc(pc.getc());
			}
		}
		if(gsm.readable()){
			led = !led;
			while(gsm.readable()){
				pc.putc(gsm.getc());
			}
		}
	}*/
}

//Kaikki mitä pc:lle kirjoitetaan lähetetään GSM:lle ja päinvastoin.
void lahetaLueLoop(bool alku){
	while(true){
		if(pc.readable()){
			led = !led;
			while(pc.readable()){
				gsm.putc(pc.getc());
			}
		}
		if(gsm.readable()){
			led = !led;
			while(gsm.readable()){
				pc.putc(gsm.getc());
			}
		}
		if(alku && !button){
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
		if(aika < ajastin.read()){
			ajastin.stop();
			ajastin.reset();
			break;
		}
	}
}

void laheta(char *kasky){
	gsm.puts(kasky);
	gsm.puts("\r");
	// pc.puts(kasky);
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