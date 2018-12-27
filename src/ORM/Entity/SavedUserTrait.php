<?php
/**
 * SavedUserTrait.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Entity;

/**
 * SavedUser trait
 */
trait SavedUserTrait
{
    /**
     * created_user
     *
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $createdUser;
    
    /**
     * updated_user
     *
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $updatedUser;
    
    /**
     * get created_user
     *
     * @return AdminUser
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }
    
    /**
     * set created_user
     *
     * @param AdminUser $createdUser
     * @return void
     */
    public function setCreatedUser(AdminUser $createdUser)
    {
        $this->createdUser = $createdUser;
    }
    
    /**
     * get updated_user
     *
     * @return AdminUser
     */
    public function getUpdatedUser()
    {
        return $this->updatedUser;
    }
    
    /**
     * set updated_user
     *
     * @param AdminUser $updatedUser
     * @return void
     */
    public function setUpdatedUser(AdminUser $updatedUser)
    {
        $this->updatedUser = $updatedUser;
    }
}