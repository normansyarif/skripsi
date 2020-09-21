#include<SoftwareSerial.h>
#include <dht11.h>
#include <i2cmaster.h>

#define DHT11PIN 2

// Esp8266
#define espRX 10
#define espTX 11

// Web server
#define host "192.168.43.28"
#define url "/api/write?key="
#define key "abcde"
#define tempSensorId "&28="
#define co2SensorId "&27="

SoftwareSerial serialEsp(espRX,espTX);
dht11 DHT11;


void setup() {
   i2c_init();
   Serial.begin(9600);
   serialEsp.begin(9600);
   
   String h = "{\"h\":\"";
   h += host;
   h += "\"}";
   Serial.print(h);
   delay(100);

   String u = "{\"u\":\"";
   u += url;
   u += "\"}";
   Serial.print(u);
   delay(100);
  
   String k = "{\"k\":\"";
   k += key;
   k += "\"}";
   Serial.print(k);
   delay(100);
   
//   connectWifi();
//   Serial.print("TCP/UDP Connection…\n");
   sendCommand("AT+CIPMUX=0\r\n",2000); // reset module
   delay(1000);
}

void loop() {
  sendData(); 
  delay(5000);
}

void connectWifi() {
   Serial.print("Restart Module…\n");
   sendCommand("AT+RST\r\n",2000); // reset module
   delay(5000);
   Serial.print("Set wifi mode : STA…\n");
   sendCommand("AT+CWMODE=1\r\n",1000); // configure as access point
   delay(5000);
   Serial.print("Connect to access point…\n");
   sendCommand("AT+CWJAP=\"Hatsune39\",\"abc12345\"\r\n",3000);
   delay(5000);
   Serial.print("Check IP Address…\n");
   sendCommand("AT+CIFSR\r\n",1000); // get ip address
   delay(5000);
}

float getTemperature() {
  int chk = DHT11.read(DHT11PIN);
  return DHT11.temperature;
}

int getCo2()
{ 
  i2c_start(0xE0);
  i2c_write(0x41);
  i2c_stop(); 
  delay(10); 
  i2c_start(0xE1);
  int temp1 = i2c_read(1);
  int temp2 = i2c_read(0);
  int sensor = (temp1 * 256) + temp2;
  sensor = sensor * 9.424; // 9.424 mewakili 1 karakter ADC, didapat dari (10000 - 350) / 1024
  return sensor;
}


void sendData() {
  float temp = getTemperature();
  int co2 = getCo2();

  // Temp for testing only
  String res = "{\"d\":\"";
  res += tempSensorId;
  res += temp;
  res += co2SensorId;
  res += co2;
  res += "\"}";
  Serial.print(res);
  
  // Initialize server

//  String cmd = "AT+CIPSTART=\"TCP\",\"";
//  cmd += host;
//  cmd += "\",80\r\n";
//    
//  sendCommand(cmd, 1000);
//  delay(100);
//
//  String cmd2 = "GET ";
//  cmd2 += url;
//  cmd2 += tempSensorId;
//  cmd2 += temp;
//  cmd2 += co2SensorId;
//  cmd2 += co2;
//  cmd2 += " HTTP/1.0\r\n\r\n";

//  Serial.print("Command: ");
//  Serial.println(cmd2);
    
//  String pjg="AT+CIPSEND=";
//  pjg += cmd2.length();
//  pjg += "\r\n";
//
//  sendCommand(pjg, 1000);
//  delay(100);
//
//  sendCommand(cmd2, 1000);
//  delay(100);
}

void sendCommand(String cmd, int timeout) {
  String response = "";
  serialEsp.print(cmd);

  long int time = millis();

  while((time+timeout) > millis()) {
    while(serialEsp.available()) {
      Serial.print(serialEsp.read());
      char c = serialEsp.read();
      response += c;
    }
  }
  
  Serial.print(response);
}
