<?php

require_once __DIR__ . '/../entity/Db.php';

Class Helper{
    
    /**
     *  Get FB Service
     *          Return an instance of the Facebook sdk
     *  @return fb instance of facebook sdk
     */
    public static function getFBService(){
        $fb = new Facebook\Facebook([
            'app_id' => '1418106458217541',
            'app_secret' => '951fc8f75cad3716a15efd1f4f053647',
            'default_graph_version' => 'v2.8',
        ]);

        return $fb;
    }

    /**
     * GetToken
     *    Return a token
     * @return token which is a String
     */
    function getToken($request){
        $data = $request->getParsedBody();
        $token = filter_var($data['token']);
        
        if(isset($token)){
            return $token;
        } else {
            return false;
        }
    }

    /**
     *  Get ID
     *          Return the user id
     *  @param request HTTP Request
     *  @param paramName string, name of the parameter 
     *  @return the parameter 
     */
    public static function getID($request, $paramName){
        $data = $request->getParsedBody();
        $id = filter_var($data[$paramName]);

        return $id;
    }

    /**
     *  Instance FB App
     *          Return an instance of the FB Appliations
     *  @return instance of Facebook application
     */
    public static function instanceFBApp(){
        $fbApp = new Facebook\FacebookApp('1418106458217541', '951fc8f75cad3716a15efd1f4f053647');

        return $fbApp;
    }

    public static function retrieveToken($userID){
         $db = new Db();
        try{
            $con = $db->connect();
            $stmt = $con->prepare("SELECT * FROM user_trace WHERE id_user = :userID");
            $stmt->bindParam(":userID", $userID);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $token = false;
   
            if($result['token']){
                $token = $result['token'];
                return $token;
            }
                
             if(!$token)
                 return 'error';

        } catch(PDOException $e){
            return $e;
        }
    }

    /**
     *  Get Config Value
     *          Return the config options from the config.json
     *  @param String key, a key representing the key present in the JSON
     */
    public static function getConfigValue($key){
        $jsonData = json_decode(file_get_contents("./config.json"));
        return $jsonData -> $key;
    }

    /**
     *  Admin Workflow
     *          Function which wrap the admin workflow (checking login + token in one function)
     *  @param userID 
     *  @return boolean
     */
    public static function adminWorkflow($userID){
        $isAdmin = AdminController::checkIfAdmin(NULL, $userID);
        $isTokenValid = AdminController::checkTokenValidity($userID);

        if(!$isAdmin || !$isTokenValid)
            return false;

        return true;
    } 

    /**
     *  Response Handler
     *
     */
     public static function responseHandler($response, $var){
         if(is_string($var)){
             return $response->withJson(array('error' => $var));
         } 
         if(is_bool($var))
            if(!$var)
                return $response->withJson(array('error' => $var));
            else 
                return $response->withJson(array('status' => 'ok'));
            
        return $response->withJson($var);
     }
}

