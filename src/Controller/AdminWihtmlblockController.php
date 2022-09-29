<?php
namespace Wihtmlblock\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminWihtmlblockController extends FrameworkBundleAdminController
{
    public function renderForm()
    {
        return $this->render('@Modules/wihtmlblock/templates/admin/form.twig', [
            'title' => $this->trans('Insert HTML code here', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'submit' => $this->trans('Submit', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'route' => '/modules/wihtmlblock/src/script/test.js'
        ]);
    }

    public function uploadForm()
    {
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = 'UPDATE `' . _DB_PREFIX_ . 'tpl_content` SET content ="' . $_POST['content'] . '"
        WHERE id=1';
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters 
        $statement->bindValue('status', 1);
        $statement->execute();

        return $this->render('@Modules/wihtmlblock/templates/admin/form.twig', [
            'title' => $this->trans('Insert HTML code here', 'Modules.Wihtmlblock.Adminwihtmlblock', []),
            'submit' => $this->trans('Submit', 'Modules.Wihtmlblock.Adminwihtmlblock', [])
        ]);
    }
}
?>