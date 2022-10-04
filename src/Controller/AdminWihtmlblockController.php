<?php
namespace Wihtmlblock\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminWihtmlblockController extends FrameworkBundleAdminController
{
    public function renderForm()
    {
        $em = $this->getDoctrine()->getManager();
        
        $statement = $em->getConnection()->prepare('SELECT * FROM`' . _DB_PREFIX_ . 'tpl_content`');
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();
        
        $statement2 = $em->getConnection()->prepare('SELECT * FROM`' . _DB_PREFIX_ . 'hook`');
        // Set parameters 
        $statement2->bindValue('status', 1);
        $statement2->execute();

        return $this->render('@Modules/wihtmlblock/templates/admin/form.twig', [
            'title' => $this->trans('Insert HTML code here', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'submit' => $this->trans('Submit', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'toggle' => $this->trans('Toggle', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'delete' => $this->trans('Delete', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'route' => '/modules/wihtmlblock/src/script/test.js',
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

        return $this->forward('Wihtmlblock\Controller\AdminWihtmlblockController::renderForm');
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

        return $this->forward('Wihtmlblock\Controller\AdminWihtmlblockController::renderForm');
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

        

        return $this->forward('Wihtmlblock\Controller\AdminWihtmlblockController::renderForm');
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

        return $this->forward('Wihtmlblock\Controller\AdminWihtmlblockController::renderForm');
    }

    public function hookBlock()
    {
        $em = $this->getDoctrine()->getManager();

        $statement3 = $em->getConnection()->prepare('SELECT * FROM `'._DB_PREFIX_.'module` WHERE name="wihtmlblock"');
        // Set parameters 
        $statement3->bindValue('status', 1);
        $statement3->execute();
        $query2 = $statement3->fetchAll();
        $custdata2;
        foreach ($query2 as $row)
        {
            $custdata2 = $row['id_module'];
        }

        
        $statement = $em->getConnection()->prepare('INSERT INTO `' . _DB_PREFIX_ . 'hook_module` (`id_module`, `id_shop`, `id_hook`)
        VALUES (' . $custdata2  . ', 1, ' . $_POST['hook'] . ')');
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

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

        $statement = $em->getConnection()->prepare('UPDATE `' . _DB_PREFIX_ . 'tpl_content` SET hook ="' . $custdata . '"
        WHERE id=' . $_POST['block']);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        return $this->forward('Wihtmlblock\Controller\AdminWihtmlblockController::renderForm');
    }
}
?>