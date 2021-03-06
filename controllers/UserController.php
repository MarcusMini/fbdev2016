<?php


require_once __DIR__.'/../entity/Contest.php';
require_once __DIR__.'/../service/helper.php';

Class UserController{
    private $contest;
    private $photo;

    function __construct(){
        $this->contest = new Contest();
    }

    /**
     * Verifie si l'utilisateur est dans le concours
     * @var idUser L'identifiant de l'utilisateur
     * @var idContest L'identifiant de notre concours
     * @return {Boolean}, vrai si l'utilisateur est dans le contest et faux sinon
    */
    public function inContest($idUser, $idContest){
        if(empty($idUser) || empty($idContest) || !is_int($idUser) || !is_int($idContest)) return false;
        $results = $this->contest->getContestOfUser($idUser);
    
        foreach ($results as $key => $value) {
            if($value['id_contest'] == $idContest)
                if($value['id_user'] == $idUser) 
                    return true;
        }

        return false;
    }

    /**
     * Ajoute avec une verification l'image de l'utilisateur dans le concours
     * @var idUser L'identifiant de l'utilisateur
     * @var idContest L'identifiant de notre concours
     * @var idPhoto L'identifiant de la photo a ajoute a notre concours
     * @return Boolean, vrai dans les cas
     */
    public function addToContest($idContest, $idUser, $idPhoto){
        if(empty($idUser) || empty($idContest) || !is_int($idUser) || empty($idPhoto) || !is_int($idContest)){
            return "invalid params";
        }
            
        if(!$this->inContest($idUser,$idContest)){
            $res = $this->contest->addPhotoToContest($idContest,$idUser,$idPhoto);
            return $res;
        }

        $resUpdate = $this->contest->updatePhotoToContest($idContest,$idUser,$idPhoto);

        return $resUpdate;
    }

    /**
     *  Get Permission 
     *          Send back the permission
     */
    public function getPermission($request){
        $userID = Helper::getID($request, 'userID');
        $token = Helper::retrieveToken($userID);

        if($token){
            $fbReq = new FacebookServices('/'.$userID.'/permissions', $token, 'GET', null);
            $res = $fbReq->make();
        }

        return $res;
    }

    public function sharePost($request){
        $contest = $this->contest->getCurrentContest();
        

        $message = Helper::getID($request, 'message');
        $link = Helper::getID($request, 'link');
        $privacy = Helper::getID($request, 'privacy');
        $userID = Helper::getID($request, 'userID');

        $token = Helper::retrieveToken($userID);
        $data = array(
            'link' => 'https://berseck.fbdev.fr/',
            'picture' => $link,
            'privacy' => array(
                'value' => $privacy
            )
        );

        $fbReq = new FacebookServices('/me/feed', $token, 'POST', $data);
        $res = $fbReq->make();

        return $res;
    }

    public function getColor(){
        //print_r($this->contest->getActiveStyle());
        $this->colorData = $this->contest->getActiveStyle()[0]['color'];
        return $this->colorData;
    }
}