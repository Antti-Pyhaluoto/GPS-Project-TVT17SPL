#include "mbed.h"
#include <string>
//#include "slre.cpp"
#include "math.h"

//#include "GSM.h"

Serial gsm(D14, D15);
Serial pc(SERIAL_TX, SERIAL_RX);
/*
int main(){
	pc.printf("Aloitus.");
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
	}
}


*/
 
//Serial pc(USBTX,USBRX);

int main()
{
    
    pc.baud(9600);
    
    pc.printf("Käynnistys");
    char buffer[256];
    
    while(true) {
		
		for(int i = 0; i< 256; i++)
		{
		
		char c = gsm.getc();
		
		buffer[i] = c;
		
		//pc.printf("%d", i);
		
		}
		pc.puts(buffer);
		
		
	


		
		//////////////////////////////////
		//char input[] = "$GPGVA,111604.00,6500.02320,N,02530.55534,E,1,05,3.74,24.8,M,20.9,M,,*69";
		
		char *token;		
		token = strtok(buffer, ",.");
		pc.printf("token = %s\n", token);
		
		
		
		
		
		
		
		char* paikannusTieto = {"$GPGGA"};
		
		for(int i = 0; i < 5; i++)
		{
			if (paikannusTieto[i] != token[i])
			{
				pc.printf("Lopeta");
				return 0;
				
			}
		}
		
		
			
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int fix = atoi(token);
		int aika = fix + 20000;
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int nolla = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int kulma = (atoi(token));					//JAA 100
		float todellinenKulma = kulma/100;			// 65 !!
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		float minuutit = atoi(token);
		float todellisetMinuutit = minuutit/100000;	//0.0232000
		
		float latitude = todellinenKulma + (todellisetMinuutit / 60);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int Korkeus = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		float a6 = atoi(token);						//2530.000000				
		float LeveysKulma = a6/100;
		LeveysKulma = floor(LeveysKulma);			//Pyöristää kahden desimaalin kokonaisluvuksi
		
		float valivaihe = a6 - (LeveysKulma * 100);		// = 30 MINUUTIT
		
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		float a7 = atoi(token);
		float leveydenAika= a7/100000;				// 0,55534
		
		float longitude = LeveysKulma + (valivaihe + leveydenAika) / 60;
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);			//EAST
		int leveysSuunta = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a9 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a10 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a11 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a12 = atoi(token);
	 
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a13 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a14 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a15 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a16 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a17 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a18 = atoi(token);
		
		token = strtok(NULL, ",.");
		pc.printf("token = %s\n", token);
		int a19 = atoi(token);
		
		
		pc.printf("aika=%d\n nolla=%d\n todellinenKulma=%f\n todellisetMinuutit=%f\n Korkeus=%d\n LeveysKulma= %f\n a7=%f\n leveysSuunta=%d\n a9=%d a10=%d\n a11=%d; a12=%d a13=%d; a14=%d; a15=%d a16=%d; a17=%d; a18=%d; a19=d;\n", aika,nolla,todellinenKulma,todellisetMinuutit,Korkeus,LeveysKulma,a7,leveysSuunta,a9,a10,
		a11,a12,a13,a14,a15,a16,a17,a18,a19);
		
		pc.printf("longitude = %f \n Latitude= %f", longitude, latitude);
	}
}

