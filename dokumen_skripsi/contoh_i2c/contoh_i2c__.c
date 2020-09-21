/*****************************************************
This program was produced by the
CodeWizardAVR V1.25.2 Evaluation
Automatic Program Generator
© Copyright 1998-2006 Pavel Haiduc, HP InfoTech s.r.l.
http://www.hpinfotech.com

Project : 
Version : 
Date    : 24/04/2008
Author  : Freeware, for evaluation and non-commercial use only
Company : 
Comments: 


Chip type           : ATmega8535
Program type        : Application
Clock frequency     : 4,000000 MHz
Memory model        : Small
External SRAM size  : 0
Data Stack size     : 128
*****************************************************/

#include <mega8535.h>   
#include <delay.h>


// I2C Bus functions
#asm
   .equ __i2c_port=0x12 ;PORTD
   .equ __sda_bit=2
   .equ __scl_bit=3
#endasm
#include <i2c.h>

// Declare your global variables here   

unsigned char bacaData_I2C(unsigned char alamatI2C) {
   
   unsigned char temp;
     
   i2c_start();              
   i2c_write(alamatI2C);     
   i2c_write(0x30);          
   i2c_stop();      

   delay_ms(250);   

   i2c_start();    
   i2c_write(alamatI2C | 0x01);  
   temp = i2c_read(0);  
   i2c_stop();     
   
   return temp;
}

void kalibrasiSensor_I2C(unsigned char alamatI2C, unsigned char sensorKe) {
     
   i2c_start();
   i2c_write(alamatI2C);
   i2c_write(0x31);
   i2c_write(sensorKe);   
   i2c_stop();
}

unsigned char bacaAlamatI2C_I2C(unsigned char alamatI2C) {

   unsigned char temp;
        
   i2c_start();
   i2c_write(alamatI2C);
   i2c_write(0x33);
   i2c_stop();
  
   delay_ms(250);
   
   i2c_start();
   i2c_write(alamatI2C | 0x01);
   temp = i2c_read(0);
   i2c_stop();
   
   return temp;
}

void ubahAlamatI2C_I2C(unsigned char alamatI2C, unsigned char newAddress) {

   i2c_start();
   i2c_write(alamatI2C);
   i2c_write(0x32);
   i2c_write(0xAA);   
   i2c_write(0x55);      
   i2c_write(newAddress);   
   i2c_stop();
        
}


void main(void)
{
// Declare your local variables here
unsigned char address,data;

// I2C Bus initialization
i2c_init();

delay_ms(500);
                              
//data = bacaData_I2C(0xE0);            // Perintah untuk membaca data digital, hasil deteksi sensor

//kalibrasiSensor_I2C(0xE0,1);          // Perintah kalibrasi untuk sensor ke-1

//kalibrasiSensor_I2C(0xE0,2);          // Perintah kalibrasi untuk sensor ke-2

//ubahAlamatI2C_I2C(0xE0,0xE2);         // Perintah untuk mengubah alamat I2C dari 0xE0 menjadi 0xE2

//address = bacaAlamatI2C_I2C(0xE0);    // Perintah untuk memastikan bahwa alamat I2C sekarang adalah 0xE0


while (1)
      {
      // Place your code here

      };
}
