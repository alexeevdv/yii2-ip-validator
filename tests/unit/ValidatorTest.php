<?php

class ValidatorTest extends \yii\codeception\TestCase {

    public $appConfig = "@tests/unit/_config.php";

    /**
     * Validate list of ip addresses
     */
    public function testIp() {

        $validator = new \alexeevdv\ip\Validator;

        $good = [
            "127.0.0.1",
            "0.0.0.0",
            "127.0.1.1",
            "120.60.15.1",
            "127.255.255.255",
            "255.255.255.255",
        ];    

        foreach($good as $ip) {
            $this->assertTrue($validator->validate($ip), $ip);
        }

        $bad = [
            "337.0.0.1",
            "11111",
            "asdf",        
        ];

        foreach($bad as $ip) {
            $this->assertFalse($validator->validate($ip), $ip);
        }
    }

    /**
     * Check if ip is in range
     */
    public function testRange() {

        try {
            $validator = new \alexeevdv\ip\Validator([
                "range" => [
                    "345.12.43.56",
                    "asdf/13",
                    "192.168.0.1/az",
                    "192.168.0.1 /22",
                    "192.168.0.1/ 22",
                    "192.168.0.1/0",
                    "123123",
                    "....",
                    "adsf df ",
                ],
            ]);
            
            $this->fail("\yii\base\InvalidConfigException expected");
            
        } catch (\yii\base\InvalidConfigException $e) {            
        }
        
        $validator = new \alexeevdv\ip\Validator([
            "range" => [
                "192.168.0.1",  // simple address
                "10.60.0.0/16", // subnet
            ],
        ]);

        $good = [
            "192.168.0.1",
            "10.60.1.3",
        ];

        foreach($good as $ip) {
            $this->assertTrue($validator->validate($ip), $ip);
        }

        $bad = [
            "192.168.0.2",
            "10.61.0.1",
        ];

        foreach($bad as $ip) {
            $this->assertFalse($validator->validate($ip), $ip);
        }
    }
    
    /**
     * Check reserved addresses.
     * 0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24 and 224.0.0.0/4.
     */
    public function testReserved() {
        $validator = new \alexeevdv\ip\Validator([
            "allowReserved" => false,
        ]);
    
        $bad = [
            "0.0.0.0",
            "0.1.2.3",
            "169.254.0.0",
            "169.254.3.64",
            "192.0.2.6",
            "224.56.110.25",            
        ];
        
        foreach($bad as $ip) {
            $this->assertFalse($validator->validate($ip), $ip);
        }        
    }
    
    /**
     * Check private addresses.
     * 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16
     */
    public function testPrivate() {
        $validator = new \alexeevdv\ip\Validator([
            "allowPrivate" => false,
        ]);
        
        $bad = [
            "10.1.2.3",
            "172.16.1.2",
            "192.168.1.2",
        ];
        
        foreach($bad as $ip) {
            $this->assertFalse($validator->validate($ip), $ip);
        }        
    }

}
