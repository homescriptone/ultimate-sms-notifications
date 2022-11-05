# Query SMS sending record document example

This project uses SMS MessageId to query SMS sending records. Documentation example, this example **cannot be debugged online**, if you need to debug it, you can download it locally and replace [AK](https://usercenter.console.aliyun.com/#/manage/ak) and debug the parameters.

## Operating conditions

- Download and unzip the code for the required language;

- Get your [credential](https://usercenter.console.aliyun.com/#/manage/ak) in your Alibaba Cloud account and use it to replace ACCESS_KEY_ID and ACCESS_KEY_SECRET in the downloaded code;

- Execute build and run statements for the corresponding language

## Steps
After downloading the code package, after changing the parameters and AK in the code according to your own needs, you can execute the following steps in the directory where the unzipped code is located.

- Java
*JDK version required 1.8*
````sh
mvn clean package
java -jar target/sample-1.0.0-jar-with-dependencies.jar
````

- TypeScript
*Node version requires 10.x and above*
````sh
npm install --registry=https://registry.npm.taobao.org && tsc && node ./dist/client.js
````

- Go
*Golang version requires 1.13 and above*
````sh
GOPROXY=https://goproxy.cn,direct go run ./main
````

- PHP
*PHP version 7.2 and above required*
````sh
composer install && php src/Sample.php
````

- Python
*Python version requires Python3*
````sh
python3 setup.py install && python ./alibabacloud_sample/sample.py
````

- C#
*.NETCORE version 2.1 and above required*
````sh
cd ./core && dotnet run
````

## APIs used

- QueryMessage uses SMS MessageId to query SMS sending records. For documentation examples, please refer to: [Documentation](https://next.api.aliyun.com/document/Dysmsapi/2018-05-01/QueryMessage)



## return example

*The actual output structure may be slightly different, which is a normal return; the following output values ​​are only for reference, subject to the actual call*


- JSON format
````js
{
    "ErrorCode": "DELIVERED",
    "ErrorDescription": "success",
    "Message": "Hello!",
    "NumberDetail": {
        "Carrier": "CMI",
        "Country": "Hongkong, China",
        "Region": "Hong Kong"
        },
    "ReceiveDate": "Mon, 24 Dec 2018 16:58:22 +0800",
    "ResponseCode": "OK",
    "ResponseDescription": "The SMS Send Request was accepted",
    "SendDate": "Mon, 24 Dec 2018 16:58:22 +0800",
    "Status": 1,
    "To": "6581xxx810"
}
````
- XML ​​format
````xml
<QueryMessageResponse>
    <ErrorCode>DELIVERED</ErrorCode>
    <ErrorDescription>success</ErrorDescription>
    <Message>Hello!</Message>
    <NumberDetail>
        <Carrier>CMI</Carrier>
        <Country>Hongkong, China</Country>
        <Region>HongKong</Region>
    </NumberDetail>
    <ReceiveDate>Mon, 24 Dec 2018 16:58:22 +0800</ReceiveDate>
    <ResponseCode>OK</ResponseCode>
    <ResponseDescription>The SMS Send Request was accepted</ResponseDescription>
    <SendDate>Mon, 24 Dec 2018 16:58:22 +0800</SendDate>
    <Status>1</Status>
    <To>6581xxx810</To>
</QueryMessageResponse>
````