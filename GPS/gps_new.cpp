#include "mbed.h"
#include <string>
#include "math.h"


Serial gsm(D14, D15);
Serial pc(SERIAL_TX, SERIAL_RX);



int main()
{
    
    pc.baud(9600);
    
    pc.printf("Käynnistys");
    
    
		while(true) {
		pc.printf("LOOPIN ALKU!\n\n\n");
		char buffer[450];
		for(int i = 0; i< 450; i++)
		{
		
		char c = gsm.getc();
		
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
			
		break;
		}
		
	}
		while (gsm.readable())
		{
		char c = gsm.getc();
		}
		token = strtok(token, ",.");
		//pc.printf(" = %s\n", token);
			
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int fix = atoi(token);
		int aika = fix + 20000;
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int nolla = atoi(token);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int kulma = (atoi(token));					//JAA 100
		float todellinenKulma = kulma/100;			// 65 !!
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		float minuutit = atoi(token);
		float todellisetMinuutit = minuutit/100000;	//0.0232000
		
		float latitude = todellinenKulma + (todellisetMinuutit / 60);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		int Korkeus = atoi(token);
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		float a6 = atoi(token);						//2530.000000				
		float LeveysKulma = a6/100;
		LeveysKulma = floor(LeveysKulma);			//Pyöristää kahden desimaalin kokonaisluvuksi (floor=alaspäin pyöristys)
		
		float valivaihe = a6 - (LeveysKulma * 100);	// = 30 MINUUTIT
		
		
		token = strtok(NULL, ",.");
		//pc.printf("token = %s\n", token);
		float a7 = atoi(token);
		float leveydenAika= a7/100000;				// 0,55534
		
		float longitude = LeveysKulma + (valivaihe + leveydenAika) / 60;
		
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
		float HDOP = atoi(token);					//HDOP
		
		token = strtok(NULL, ",.");
	//	pc.printf("token = %s\n", token);			//Altitude from sea level
	//	int a12 = atoi(token);
	 
		pc.printf("aika = %d\n longitude = %f\n Latitude = %f HDOP= %f\n\n\n\n",aika, longitude, latitude, HDOP);
	}
}

