### How To Use ###

I promise, it is very easy

+ Create an entity

```lang=php
<?php
namespace AppBundle\Entity;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Doctrine\ORM\Mapping as ORM;
use SymfonyId\AdminBundle\Annotation as Siab;
use SymfonyId\AdminBundle\Model\ModelInterface;
use SymfonyId\AdminBundle\Model\BulkDeletableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="siab_idname")
 */
class IdName implements ModelInterface, BulkDeletableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Siab\Column()
     * @Siab\Filter()
     * @ORM\Column(name="program_name", type="string", length=77)
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = strtoupper($name);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDeleteInformation()
    {
        return $this->getName();
    }
}

```

+ Generate the CRUD

```lang=shell
php bin/console symfonyid:generate:crud AppBundle:IdName
```

And walaaaaaaaaaaaaaaa you have form and controller also to manage your entity. 

Your controller store in AppBundle/Admin and your form store in AppBundle/Form/Type

