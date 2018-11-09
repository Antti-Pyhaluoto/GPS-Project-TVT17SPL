#include "mbed.h"
//#include "GSM.h"

Serial gsm(D14, D15);
Serial pc(USBTX, USBRX);
DigitalOut led(LED1);

DigitalIn button(USER_BUTTON);

Timer ajastin;

//Alku käskyt

char *alku[][2][20] = {
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

void laheta(char *kasky);
void lahetaJaLue(char *kasky, int aika);
int lahetaJaOdota(char *kasky, char *vastaus, int aika);

int main(){
	pc.printf("Aloitus. GSM serial pass.\n");
	//gsm.printf("AT\r");
	while(true){
		if(!button)break;
	}
	while(true){
		if(button)break;
	}
	int l = sizeof(alku)/sizeof(alku[0]);
	
	for(int i = 0; i < l - 1; i++){
		int j = lahetaJaOdota(alku[i][0][0], alku[i][1][0], 5);
		wait(0.5);
		if(j != 0 )i--;
	}
	pc.printf("For valmis\n");
	pc.puts(alku[l - 1][0][0]);
	laheta(alku[l - 1][0][0]);
	
	pc.printf("\nValmis\n");
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