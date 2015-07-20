<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 09:45
 */

namespace Test\Controller;

use Common\Api\AccountApi;
use Think\Controller;
use Uclient\Model\OAuth2TypeModel;

/**
 *
 *
 * @Test AccountApi
 * Class TestAccountApiController
 * @package Test\Controller
 *
 */
class TestAccountApiController extends Controller {

    /**
     *
     */
    public  function index(){
        import("Org.String");

        $username = \String::randString(9,0);

        $password = \String::randString(6);

        $entity = [
            'username'=>$username,
            'password'=>$password,
            'from'=>OAuth2TypeModel::OTHER_APP,
            'email'=>'',
            'phone'=>'',
        ];

        $result =  AccountApi::REGISTER($entity);
        
        $this->ajaxReturn($result,"xml");
    }


    public function getInfo(){

        $result = apiCall(AccountApi::GET_INFO,array(1));

        $this->ajaxReturn($result,"xml");

    }

}