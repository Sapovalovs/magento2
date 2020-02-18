<?php

namespace Dev\ProductComments\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Dev\ProductComments\Model\ResourceModel\Comment as Commentfactory;

class Comment extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'product_comments';
    const NAME = 'name';
    const IDFILEDNAME = 'comment_id';
    const EMAIL = 'email';
    const COMMENT = 'comment';
    const STATUS = 'status';
    protected $cacheTag = 'product_comments';
    protected $_eventPrefix = 'product_comments';
    protected function _construct()
    {
        $this->_init(Commentfactory::class);
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::IDFILEDNAME);
    }
    /**
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        $this->setData(self::IDFILEDNAME, $id);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }
    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
    }
    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }
    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->setData(self::COMMENT, $comment);
    }
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
