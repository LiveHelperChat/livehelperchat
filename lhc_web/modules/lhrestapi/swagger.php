<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

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
         "name":"api",
         "description":"Live Helper Chat API"
      }
   ],
   "schemes":[
      "https"
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
      },
      "/restapi/addmsguser":{
         "post":{
            "tags":[
               "api"
            ],
            "summary":"Add message to chat as a user",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"msg",
					"in":"formData",
					"description":"Message",
					"required":true,
					"type":"string"
               },
               {
                    "name": "chat_id",
                    "in": "formData",
                    "description": "Chat ID",
                    "required": true,
                    "type": "string"
               },
               {
                    "name": "hash",
                    "in": "formData",
                    "description": "Hash",
                    "required": true,
                    "type": "string"
               }
            ],
            "responses": {
                "200": {
                    "description": "Message added",
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
      },
      "/restapi/logout":{
         "post":{
            "tags":[
               "api"
            ],
            "summary":"Revokes token if it's found",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"token",
					"in":"formData",
					"description":"Token",
					"required":true,
					"type":"string",
					"format":"int32"
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
      },
      "/restapi/fetchchat":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Fetch chat information",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[               
               {
                    "name":"chat_id",
					"in":"query",
					"description":"Chat ID",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"hash",
					"in":"query",
					"description":"Hash, optional variable. If provided it will be validated against chat hash also.",
					"required":false,
					"type":"string",
					"format":"int32"
               }
            ],
            "responses": {
                "200": {
                    "description": "Fetch chat information",
                    "schema": {
                    	
                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {
                    	
                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }         
      },
      "/restapi/fetchchatmessages":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Fetch chat messages",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"chat_id",
					"in":"query",
					"description":"Chat ID",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"workflow",
					"in":"query",
					"description":"Should workflow be executed. Like auto responder. Usefull if you are building visitors custom interface.",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"last_message_id",
					"in":"query",
					"description":"Last message ID from which should be returned next messages.",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"ignore_system_messages",
					"in":"query",
					"description":"Should system messages be ignored or not.",
					"required":false,
					"type":"string",
					"format":"int32"
               }
            ],
            "responses": {
                "200": {
                    "description": "Fetch chat messages",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/getuser":{
         "post":{
            "tags":[
               "api"
            ],
            "summary":"Fetches user and verifies provided password",
            "description":"If user_id is provided. Systems tries to find provided user. If not system tries to find user by email or username. If password is provided it verifies provided password.",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"user_id",
					"in":"query",
					"description":"User ID",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"username",
					"in":"query",
					"description":"Username",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"email",
					"in":"query",
					"description":"E-mail",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"password",
					"in":"query",
					"description":"Password",
					"required":false,
					"type":"string",
					"format":"int32"
               }
            ],
            "responses": {
                "200": {
                    "description": "Fetch chat messages",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/getusers":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Fetches users",
            "description":"Returns list of all system users.",
            "produces":[
               "application/json"
            ],
            "parameters":[
            ],
            "responses": {
                "200": {
                    "description": "",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/isonline":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Check is someone online",
            "description":"Returns global status is anyone online",
            "produces":[
               "application/json"
            ],
            "parameters":[
            ],
            "responses": {
                "200": {
                    "description": "",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/isonlinedepartment/{department_id}":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Check is department online",
            "description":"Returns status for particular department",
            "produces":[
               "application/json"
            ],
            "parameters":[
                {
                    "name":"department_id",
                    "in":"path",
                    "description":"Department ID",
                    "required":false,
                    "type":"string",
                    "format":"int32"
                }
            ],
            "responses": {
                "200": {
                    "description": "",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/isonlineuser/{user_id}":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Check is user online",
            "description":"Returns status for particular user",
            "produces":[
               "application/json"
            ],
            "parameters":[
                {
                    "name":"user_id",
                    "in":"path",
                    "description":"User ID",
                    "required":false,
                    "type":"string",
                    "format":"int32"
                }
            ],
            "responses": {
                "200": {
                    "description": "",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/updatechatattributes":{
         "post":{
            "tags":[
               "api"
            ],
            "summary":"Updates chat attributes",
            "description":"Updates chat attributes.",
            "produces":[
               "application/json"
            ],
            "parameters":[
                {
                    "name":"chat_id",
                    "in":"formData",
                    "description":"Chat ID",
                    "required":true,
                    "type":"string"
                },
                {
                    "name": "hash",
                    "in": "formData",
                    "description": "Hash",
                    "required": true,
                    "type": "string"
                },
                {
                    "name": "data",
                    "in": "formData",
                    "description": "Data as encoded JSON format.",
                    "required": true,
                    "type": "string"
                }
            ],
            "responses": {
                "200": {
                    "description": "",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/chats":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Fetch chats list",
            "description":"",
            "produces":[
               "application/json"
            ],
            "parameters":[
               {
                    "name":"departament_id",
					"in":"query",
					"description":"Department ID",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"user_id",
					"in":"query",
					"description":"User ID",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"status",
					"in":"query",
					"description":"Status",
					"required":false,
					"type":"string",
					"format":"int32"
               },
                {
                    "name":"limit",
					"in":"query",
					"description":"Limit",
					"required":false,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"offset",
					"in":"query",
					"description":"Offset",
					"required":false,
					"type":"string",
					"format":"int32"
               }
            ],
            "responses": {
                "200": {
                    "description": "Fetch chat list",
                    "schema": {

                    }
                },
                "400": {
                    "description": "Error",
                    "schema": {

                    }
                }
            },
            "security": [
                {
                    "login": []
                }
            ]
         }
      },
      "/restapi/loginbytoken":{
         "get":{
            "tags":[
               "api"
            ],
            "summary":"Logins user by token",
            "description":"",            
            "parameters":[
               {
                    "name":"token",
					"in":"query",
					"description":"Token",
					"required":true,
					"type":"string",
					"format":"int32"
               },
               {
                    "name":"r",
					"in":"query",
					"description":"Redirect url",
					"required":false,
					"type":"string",
					"format":"int32"
               },              
               {
                  "name":"api",
                  "description":"Is it api mode",
                  "required":false,
                  "type":"boolean",                      
                  "default":true,                      
                  "in":"query"
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
   "securityDefinitions": {
        "login": {
            "type": "basic",
            "description": "Basic authentication"
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