#include "mbed.h"
#include "GSM.h"

Serial gsm(D14, D15);
Serial pc(USBTX, USBRX);

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