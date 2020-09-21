const express = require('express');
const request = require('request');
const SerialPort = require("serialport");
const app = express();

app.get('/', (req, res) => {
	res.send('It works!');
});

function sendApi(url) {
	console.log("Req: " + url);
	request("http://" + url, { json: true }, (err, res, body) => {
	  if (err) { return console.log(err); }
	  console.log("Res: " + JSON.stringify(body) + "\n");
	});
}

var port = 3000;
var arduinoCOMPort = "/dev/ttyUSB0";

var arduinoSerialPort = new SerialPort(arduinoCOMPort, {  
 baudRate: 9600
});

arduinoSerialPort.on('open',function() {
  console.log('Serial Port ' + arduinoCOMPort + ' is opened.');

  let counter = 0;
  let url = "";

  arduinoSerialPort.on('data', function(data) {
    // console.log("Data #" + counter + ": " + data);

    data = JSON.parse(data);

    if(data.hasOwnProperty('h')){
    	// url += data.h;
    	url += 'localhost';
	}else if(data.hasOwnProperty('u')){
    	url += data.u;
	}else if(data.hasOwnProperty('k')){
    	url += data.k;
	}else if(data.hasOwnProperty('d')) {
		let apiUrl = url + data.d;
		sendApi(apiUrl);
	}else{
		console.log("Unidentified json key");
	}

    // counter++;
  });
});

app.listen(5000);
