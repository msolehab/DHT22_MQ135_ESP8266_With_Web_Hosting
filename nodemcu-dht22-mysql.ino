#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <DHT.h>
#include <MQ135.h>

#define DHTPIN D3
#define DHTTYPE DHT22
#define PIN_MQ135 A0

DHT dht(DHTPIN, DHTTYPE);
MQ135 gasSensor = MQ135(PIN_MQ135);

const char* ssid     = "*****";
const char* password = "*****";

const char* SERVER_NAME = "your_url";
String PROJECT_API_KEY = "tempQuality";

unsigned long lastMillis = 0;
long interval = 30000; // interval in milliseconds (30 seconds)

void setup() {
    Serial.begin(115200);
    Serial.println("Connecting to WiFi");

    dht.begin();

    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }

    Serial.println("");
    Serial.println("WiFi connected");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
}

void loop() {
    if (WiFi.status() == WL_CONNECTED) {
        if (millis() - lastMillis > interval) {
            float temperature = dht.readTemperature();
            float humidity = dht.readHumidity();

            if (isnan(temperature) || isnan(humidity)) {
                Serial.println("Failed to read from DHT sensor!");
            } else {
                
                float gas_level = gasSensor.getCorrectedPPM(temperature, humidity); // Assuming MQ135 analog output is connected to A0
                int air_quality = getAirQuality(gas_level);

                sendSensorData(temperature, humidity, gas_level, air_quality);
            }

            lastMillis = millis();
        }
    } else {
        Serial.println("WiFi Disconnected");
        WiFi.begin(ssid, password); // Reconnect to WiFi if disconnected
    }

    delay(2000); // Delay between sensor readings
}

int getAirQuality(float gas_level) {
    // Implement your logic to determine air quality based on gas level
    // Example logic:
    if (gas_level < 500) {
        return 1; // High
    } else if (gas_level >=500 || gas_level<=1000) {
        return 2; // Moderate
    } else {
        return 3; // Low
    }
}

void sendSensorData(float temperature, float humidity, float gas_level, int air_quality) {
    WiFiClient client;
    HTTPClient http;

    String postData = "api_key=" + PROJECT_API_KEY;
    postData += "&temperature=" + String(temperature, 2);
    postData += "&humidity=" + String(humidity, 2);
    postData += "&gas_level=" + String(gas_level, 2);
    postData += "&air_quality=" + String(air_quality);

    http.begin(client, SERVER_NAME);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
    } else {
        Serial.print("Error in HTTP request: ");
        Serial.println(httpResponseCode);
    }

    http.end();
}
