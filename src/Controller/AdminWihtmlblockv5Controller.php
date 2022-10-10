<?php
namespace Wihtmlblockv5\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\Shop\Context;

class AdminWihtmlblockv5Controller extends FrameworkBundleAdminController
{
    public function renderForm()
    {
        $em = $this->getDoctrine()->getManager();
        
        $statement = $em->getConnection()->prepare('SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content` ORDER BY hook');
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();
        
        $statement2 = $em->getConnection()->prepare('SELECT * FROM`' . _DB_PREFIX_ . 'hook`');
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();

        return $this->render('@Modules/wihtmlblockv5/templates/admin/form.twig', [
            'title' => $this->trans('Insert HTML code here', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'title2' => $this->trans('Disable or delete blocks', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'title3' => $this->trans('Assign a priority index to block', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'title4' => $this->trans('Hook a block', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'submit' => $this->trans('Submit', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'toggle' => $this->trans('Toggle', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'delete' => $this->trans('Delete', 'Modules.Wihtmlblockv5.Adminwihtmlblockv5', []),
            'route' => '/modules/wihtmlblockv5/src/script/test.js',
            'records' => $statement->fetchAll(),
            'hooks' => $statement2->fetchAll()
        ]);
    }

    public function uploadForm()
    {
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'UPDATE `' . _DB_PREFIX_ . 'tpl_content` SET content ="' . $_POST['content'] . '"
        WHERE id=' . $_POST['block'];
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        $RAW_QUERY2 = 'SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content`';
        
        $statement2 = $em->getConnection()->prepare($RAW_QUERY2);
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();

        return $this->forward('Wihtmlblockv5\Controller\AdminWihtmlblockv5Controller::renderForm');
    }

    public function addBlock()
    {
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'INSERT INTO `' . _DB_PREFIX_ . 'tpl_content` (`content`)
        VALUES ("Hello :>")';
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        $RAW_QUERY2 = 'SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content`';
        
        $statement2 = $em->getConnection()->prepare($RAW_QUERY2);
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();

        return $this->forward('Wihtmlblockv5\Controller\AdminWihtmlblockv5Controller::renderForm');
    }

    public function editBlock()
    {
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY2 = 'SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content`';
        
        $statement2 = $em->getConnection()->prepare($RAW_QUERY2);
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();

        $records = $statement2->fetchAll();

        $RAW_QUERY;

        if(isset($_POST['deleteButton']))
        {
            $RAW_QUERY = 'DELETE FROM `' . _DB_PREFIX_ . 'tpl_content`
            WHERE id=' . $_POST['block'];
        }
        else if(isset($_POST['disableButton']))
        {      
            $RAW_QUERY3 = 'SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content` WHERE id=' . $_POST['block'];
        
            $statement3 = $em->getConnection()->prepare($RAW_QUERY2);
            // Set parameters 
            $statement3->bindValue('status', 1);
            $statement3->execute();
    
            $records2 = $statement3->fetchAll();

            if($records2[0]['toggle'] == 0){
                $RAW_QUERY = 'UPDATE `' . _DB_PREFIX_ . 'tpl_content`
                SET toggle = 1 WHERE id=' . $_POST['block']; 
            }
            else{
                $RAW_QUERY = 'UPDATE `' . _DB_PREFIX_ . 'tpl_content`
                SET toggle = 0 WHERE id=' . $_POST['block']; 
            }
        }

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        

        return $this->forward('Wihtmlblockv5\Controller\AdminWihtmlblockv5Controller::renderForm');
    }

    public function indexBlock()
    {
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'UPDATE `' . _DB_PREFIX_ . 'tpl_content`
            SET orderNum =' . $_POST['number'] . ' WHERE id=' . $_POST['block']; 

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        return $this->forward('Wihtmlblockv5\Controller\AdminWihtmlblockv5Controller::renderForm');
    }

    public function hookBlock()
    {
        $em = $this->getDoctrine()->getManager();

        $statement3 = $em->getConnection()->prepare('SELECT * FROM `'._DB_PREFIX_.'module` WHERE name="wihtmlblockv5"');
        // Set parameters 
        $statement3->bindValue('status', 1);
        $statement3->execute();
        $query2 = $statement3->fetchAll();
        $custdata2;
        foreach ($query2 as $row)
        {
            $custdata2 = $row['id_module'];
        }

        $statement2 = $em->getConnection()->prepare('SELECT * FROM `'._DB_PREFIX_.'hook` WHERE id_hook=' . $_POST['hook']);
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();
        $query = $statement2->fetchAll();
        $custdata;
        foreach ($query as $row)
        {
            $custdata = $row['name'];
        }

        $statement4 = $em->getConnection()->prepare('SELECT * FROM `'._DB_PREFIX_.'hook_module` WHERE id_hook=' . $_POST['hook'] . ' AND id_module=' . $custdata2);
        // Set parameters 
        $statement4->bindValue('status', 1);
        $statement4->execute();
        $query3 = $statement4->fetchAll();
        $custdata;
        foreach ($query as $row)
        {
            $custdata = $row['name'];
        }

        $id_shop = Context::getContextShopID();

        if(sizeof($query3) == 0)
        {
            $statement = $em->getConnection()->prepare('INSERT INTO `' . _DB_PREFIX_ . 'hook_module` (`id_module`, `id_shop`, `id_hook`)
            VALUES (' . $custdata2  . ', ' . $id_shop . ', ' . $_POST['hook'] . ')');
            // Set parameters 
            $statement->bindValue('status', 1);
            $statement->execute();
        }

        $statement = $em->getConnection()->prepare('UPDATE `' . _DB_PREFIX_ . 'tpl_content` SET hook ="' . $custdata . '"
        WHERE id=' . $_POST['block']);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        return $this->forward('Wihtmlblockv5\Controller\AdminWihtmlblockv5Controller::renderForm');
    }
}
?>