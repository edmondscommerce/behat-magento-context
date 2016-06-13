<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Mage;
use Mage_Core_Model_Store;

class AdminFixture extends AbstractMagentoContext
{
    protected $_userName;
    protected $_password;

    public function createAdminUser($username, $password)
    {
        $user     = Mage::getModel('admin/user')->loadByUsername($username);
        $userData = [
            'username'  => $username,
            'firstname' => 'Behat',
            'lastname'  => 'Admin',
            'email'     => 'behat@example.com',
            'is_active' => 1
        ];
        foreach ($userData as $key => $value) {
            $user->setData($key, $value);
        }
        $user->setPassword($password);
        $user->save();
        if (!$user->hasAssigned2Role($user)) {
            $user->setRoleIds(array(1))  //Administrator role id is 1 ,Here you can assign other roles ids
                 ->setRoleUserId($user->getUserId())
                 ->saveRelations();
        }
        $this->_userName = $username;
        $this->_password = $password;
    }

    public function getAdminUrl($path = null, $params = array())
    {
        if(is_null($path)) {
            $path = 'adminhtml/index/index/';
        }
        $params = array_merge($params, ['_type' => Mage_Core_Model_Store::URL_TYPE_WEB]);
        return Mage::helper('adminhtml')
                   ->getUrl($path, $params);
    }
}
