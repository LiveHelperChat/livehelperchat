<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');
?>
{
   "swagger":"2.0",
   "info":{
      "description":"Live Helper Chat API description",
      "version":"1.0.0",
      "title":"Live Helper Chat API",
      "termsOfService":"http://swagger.io/terms/",
      "contact":{
         "email":"remdex@gmail.com",
         "name":"Live Helper Chat"
      },
      "license":{
         "name":"",
         "url":""
      }
   },
   "host":"<?php echo $_SERVER['HTTP_HOST']?>",
   "basePath":"/",
   "tags":[
      {
         "name":"callback",
         "description":"Schedule Callback API description"         
      }
   ],
   "schemes":[
      "http"
   ],
   "paths":{
      "/restapi/login":{
         "post":{
            "tags":[
               "api"
            ],
            "summary":"Login to application, also registers device if required",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"username",
					"in":"formData",
					"description":"Username",
					"required":true,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"password",
					"in":"formData",
					"description":"Password",
					"required":true,
					"type":"string"
               },
               {
                    "name":"generate_token",
					"in":"formData",
					"description":"Should we generate authentification/session token",
					"required":false,
					"type":"boolean"
               },
               {
                    "name":"device_token",
					"in":"formData",
					"description":"Device token",
					"required":false,
					"type":"string"
               },
               {
                    "name":"device",
					"in":"formData",
					"description":"Device type",
					"required":false,
					"type":"string",
					"enum": ["unknown","ios","android"]
               }
            ],
            "responses": {
                "200": {
                    "description": "Login",
                    "schema": {
                    	
                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {
                    	
                    }
                }
            }
         }         
      }
   },   
   "definitions":{      
      "Callback":{
         "type":"object",
         "properties":{
            "error":{
               "type":"boolean",
               "description":"Was there any errors processing query"
            },
            "fields":{
               "type":"array", 
               "items": {
	              "type": "string"
	           },               
               "description":"Holds field names of missing fields. This field is present only if error is true"
            },
            "msg":{
               "type": "string",
               "description": "Success or error message"               
            }
         }
      },
      "Schedule":{
         "type":"object",
         "properties":{
            "error":{
               "type":"boolean",
               "description":"Was there any errors processing query"
            },
            "list":{
               "$ref": "#\/definitions\/Schedule.list",
               "description": "List object"               
            }
         }
      },
      "Schedule.list.daylist":{
         "type":"object",
         "properties":{
            "active":{
               "type":"number",
               "description":"Is day active for scheduling or not 1 || 0"
            },
            "available":{
               "type":"number",
               "description":"Is available"
            },
            "fullname":{
               "type":"string",
               "description":"Day full name. E.g Friday 24 March 2017"
            },
            "wday":{
               "type":"string",
               "description":"Day of the week. today || tomorrow || 1 || 2 || 3 || 4 || 5 || 6 || 7"
            }
         }
      },
      "Schedule.list.timelist":{
         "type":"object",
         "properties":{
            "value":{
               "type":"string",
               "description":"Example - 21:37"
            },
            "fullname":{
               "type":"string",
               "description":"Example - 9:30pm - 10:00pm"
            }
         }
      },
      "Schedule.timelistdays":{
         "type":"object",
         "properties":{
            "1":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "2":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "3":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "4":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "6":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "7":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "today":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            },
            "tomorrow":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.timelist"
               },
               "description": "Array of possible calltime for a day"
            }
         }
      },
      "Schedule.list":{
         "type":"object",
         "properties":{         
            "day_list":{
               "type": "array",
               "items": {
                    "$ref": "#\/definitions\/Schedule.list.daylist"
               },
               "description": "Array of possible daylist"
            },
            "time_list":{
               "type": "object",
               "$ref": "#\/definitions\/Schedule.timelistdays",
               "description": "List object"               
            },
            "today":{
               "type":"number",
               "description":"What day is it today from list"
            },
            "tomorrow":{
               "type":"number",
               "description":"What day would be tomorrow"
            },
            "delay":{
               "type":"boolean",
               "default" : true,
               "description":"Is scheduling callback for next available agent is possible. True - yes, false - no. If delay equal false, option should be hidden."
            },
            "unavailable":{
               "type":"boolean",
               "default": false,
               "description":"If this is true that means scheduling callback is impossible. And unavailable page should be shown."
            }
         }
      }
   },
   "externalDocs":{
      "description":"Find out more about Live Helper Chat",
      "url":"https://livehelperchat.com"
   }
}
<?php 

exit;

?>