<?php

namespace Kunstmaan\AdminNodeBundle\AdminList;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;

use Doctrine\ORM\QueryBuilder;

class PageAdminListConfigurator extends AbstractAdminListConfigurator
{
    protected $locale;
    protected $permission;

    /**
     * @param string $locale     The current locale
     * @param string $permission The permission
     */
    public function __construct($locale, $permission)
    {
        $this->locale = $locale;
        $this->setPermissionDefinition(
            new PermissionDefinition(array($permission), 'Kunstmaan\AdminNodeBundle\Entity\Node', 'n')
        );
    }

    /**
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilter("title"), "Title");
        $builder->add('online', new BooleanFilter("online"), "Online");
        $builder->add('created', new DateFilter("created"), "Created At");
        $builder->add('updated', new DateFilter("updated"), "Updated At");
    }

    public function buildFields()
    {
        $this->addField("title", "Title", true);
        $this->addField("created", "Created At", true);
        $this->addField("updated", "Updated At", true);
        $this->addField("online", "Online", true);
    }

    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanAdminNodeBundle_pages_edit',
            'params' => array('id' => $item->getNode()->getId())
        );
    }

    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminNodeBundle_pages');
    }

    public function canAdd()
    {
        return false;
    }

    public function getAddUrlFor($params = array())
    {
        return "";
    }

    public function canDelete($item)
    {
        return false;
    }

    public function getDeleteUrlFor($item)
    {
        return array();
    }

    public function getRepositoryName()
    {
        return 'KunstmaanAdminNodeBundle:NodeTranslation';
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder, $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id');
        $queryBuilder->andWhere('b.lang = :lang');
        $queryBuilder->andWhere('n.deleted = 0');
        $queryBuilder->setParameter('lang', $this->locale);
    }

}
